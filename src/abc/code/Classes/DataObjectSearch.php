<?php

class DataObjectSearch extends Object {

    /**
    * @config
    */
    private static $cache_time = null;

    public static function set_cache_time($cache_time) {
        static::$cache_time = $cache_time;
    }

    public static function get_cache_time() {
        $cacheTime = (int) static::$cache_time !== null
            ? static::$cache_time
            : Config::inst()->get('DataObjectSearch', 'cache_time');
    }

    protected static function get_blacklisted_words() {
        return array(
            'of','a','the','and','an','or','nor',
            'but','is','if','then','else','when',
            'at','from','by','on','off','for',
            'in','out','over','to','into','with',
            'also','back','well','big','when','where',
            'why','who','which', 'it', 'be', 'so', 'far',
            'one', 'our', 'we','only','they','this', 'i',
            'do'
        );
    }

    public static function str_to_terms($str) {
        $terms = array_map('trim', explode(' ', $str));
        $out = array();
        foreach ($terms as $term) {
            if (!in_array(strtolower($term), static::get_blacklisted_words())) {
                $out[] = trim($term, ',.!?');
            }
        }
        return $out;
    }

    /**
     * generates a window of text up to 4 words long
     *
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function str_to_fragments($str) {
        $terms = array_map('trim', explode(' ', $str));
        $out = array();
        foreach ($terms as $k => $term) {

            // add the raw word
            $out[] = $term;

            // extract what we can
            $prevPrev = empty($terms[$k - 2]) ? '' : $terms[$k - 2];
            $prev = empty($terms[$k - 1]) ? '' : $terms[$k - 1];
            $next = empty($terms[$k + 1]) ? '' : $terms[$k + 1];
            $nextNext = empty($terms[$k + 2]) ? '' : $terms[$k + 2];

            // fragments
            $o5 = $o3 = array();

            // generate 3 word fragment
            if ($prev) $o3[] = $prev;
            $o3[] = $term;
            if ($next) $o3[] = $next;
            $o3 = implode(' ', $o3);
            $out[] = $o3;

            // generate 5 word fragment
            if ($prevPrev) $o5[] = $prevPrev;
            $o5[] = $o3;
            if ($nextNext) $o5[] = $nextNext;
            $o5 = implode(' ', $o5);
            $out[] = $o5;
        }
        return array_unique($out);
    }

    /**
     * kind of a wierd thing to add in here
     * @param  [type] $class  [description]
     * @param  [type] $q      [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public static function search_list($class, $q, array $fields) {

        // parse terms
        $terms = array_merge(static::str_to_terms($q), static::str_to_fragments($q));
        $terms[] = $q;
        $terms = array_unique($terms);

        // generate data list
        $list = new DataList($class);
        $where = '';
        foreach ($fields as $field) {
            foreach ($terms as $term) {
                if ($where) $where .= " OR ";
                $where .= $field . " LIKE '%" . Convert::raw2sql($term) . "%'";
            }
        }

        return $list->where($where);
    }


    /**
     * kind of a wierd thing to add in here
     * @param  [type] $class  [description]
     * @param  [type] $q      [description]
     * @param  array  $fields [description]
     * @return [type]         [description]
     */
    public static function weighted_search($className, $q, array $fields = array('Content' => 1,'Title' => 3), $start = 0, $limit = 10, $filterSql = null) {

        // sort out the cache
        $implodedArgs = $className . '_' . $q . '_' . implode('_', $fields) . '_' . $start . '_' . $limit  . '_' . $filterSql;
        $cachekey = preg_replace("/[^A-Za-z0-9]/", '_', __FUNCTION__ . "_" . $implodedArgs);
        $cache = SS_Cache::Factory(
            'DataObjectSearch',
            'Output',
            array(
                'lifetime' => static::get_cache_time(),
                'automatic_serialization' => true
            )
        );
        $set = $cache->load($cachekey);

        // dont hit the db if we don't need to
        if (!$set) {

            // parse terms
            // need to analyse various fragments like first 3 words, last 3 words
            $terms = array_merge(static::str_to_terms($q), static::str_to_fragments($q));
            $terms[] = $q;
            $terms = array_unique($terms);

            // Set some vars
            $set = new ArrayList;
            $db  = AbcDB::getInstance();
            $sql = '';

            // expand search to subclasses
            $classes = array_merge(ClassInfo::subclassesFor($className), array($className));

            // iterate through list
            foreach ($classes as $className) {

                // Fetch Class Data
                $table = DataObjectHelper::getTableForClass($className);

                foreach ($fields as $field => $weight) {
                    foreach ($terms as $term) {

                        // init the recivers
                        $tables = $joins = $filter = array();

                        // $tables we are working with
                        if ($table) $tables[$table] = $table;

                        // Where
                        if ($table) $where[$table][] = $table . ".ClassName = '" . $className . "'";

                        // find the table the property is on
                        $extTable = DataObjectHelper::getExtensionTableForClassWithProperty($className, $field);

                        // join
                        if ( $table && $extTable && $table!=$extTable ) {
                            $joins[$table][] = $extTable;
                        } elseif ($extTable) {
                            $tables[$extTable] = $extTable;
                        }

                        // ext table
                        if ($extTable) {
                            $filter[$table][] = $extTable . "." . $field . " LIKE '%" . Convert::raw2sql($term) . "%'";
                        } else {
                            $filter[$table][] = $table . "." . $field . " LIKE '%" . Convert::raw2sql($term) . "%'";
                        }

                        // Build Query
                        foreach($tables as $table){

                            // Prepare Where Statement
                            $uWhere     = array_unique($where[$table]);
                            $uFilter    = array_unique($filter[$table]);

                            // Where SQL
                            $wSql = "(".implode(' OR ',$uWhere).") AND (".implode(' OR ',$uFilter).")";

                            // Make the rest of the SQL
                            if ($sql) $sql.= " ) UNION ALL ("."\n\n";
                            $sql.= "SELECT " . $table . ".ClassName, " . $table . ".ID,  " . $weight .  " AS weight" . "\n";
                            $sql.= "FROM " . $table . "\n";

                            // join
                            if (array_key_exists($table, $joins)){
                                $join = array_unique($joins[$table]);
                                foreach($join as $j){
                                    $sql .= " LEFT JOIN " . $j . " ON " . $table . ".ID = " . $j . ".ID" . "\n";
                                }
                            }

                            // Add the WHERE statement
                            $sql .= "WHERE " . $wSql . "\n\n";
                        }

                        // Add Global Filter to Query
                        if ($filterSql) {
                            $sql .= (count($tables) == 1 ? "AND " : "WHERE ") . $filterSql;
                        }
                    }
                }
            }
            // Add Limits and order to Query
            $sql = "
                SELECT SQL_CALC_FOUND_ROWS ClassName, ID, SUM(weight) AS total_weight
                FROM ((" . $sql . ")) AS t1
                GROUP BY ID
                ORDER BY total_weight DESC
                LIMIT " . $start . "," . $limit . "
            ";

            // Get Data
            // die('<br>' . $sql . '<br>');
            $result = $db->query($sql);
            $result = $result ? $result->fetchAll(PDO::FETCH_OBJ) : array() ;

            // Convert to DOs
            foreach( $result as $entry ){

                // Make the data easier to work with
                $entry         = (object) $entry;
                $className     = $entry->ClassName;

                // this is slower, but will be more reliable
                $dO = DataObject::get_by_id($className, $entry->ID);

                $set->push($dO);
            }

            // stash total length
            $set->unlimitedRowCount = $db->query('SELECT FOUND_ROWS() AS total')->fetch(PDO::FETCH_OBJ)->total;

            // cache
            $cache->save($set, $cachekey);
        }

        return $set;
    }
}

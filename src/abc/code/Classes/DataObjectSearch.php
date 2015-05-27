<?php

class DataObjectSearch {

    protected static function get_blacklisted_words() {
        return array(
            'of','a','the','and','an','or','nor',
            'but','is','if','then','else','when',
            'at','from','by','on','off','for',
            'in','out','over','to','into','with',
            'also','back','well','big','when','where',
            'why','who','which', 'it', 'be', 'so', 'far',
            'one', 'our', 'we','only','they','this'
        );
    }

    public static function str_to_terms($str) {
        $terms = array_map('trim', explode(',', $str));
        $out = array();
        foreach ($terms as $term) {
            if (!in_array(strtolower($term), static::get_blacklisted_words())) {
                $out[] = trim($term, ',.!?');
            }
        }
        return $out;
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
        $terms = static::str_to_terms($q);
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
    public static function weighted_search($className, $q, array $fields) {

        // parse terms
        $terms = static::str_to_terms($q);
        $terms[] = $q;
        $terms = array_unique($terms);

        // Set some vars
        $set         = new ArrayList;
        $db          = AbcDB::getInstance();
        $sql         = '';
        $tables = $joins = $filter = array();

        // Fetch Class Data
        $table      = DataObjectHelper::getTableForClass($className);
        $extTable   = DataObjectHelper::getExtensionTableForClassWithProperty($className, 'Tags');

        // $tables we are working with
        if ($table) $tables[$table] = $table;

        // join
        if( $table && $extTable && $table!=$extTable ){
            $joins[$table][] = $extTable;
        }elseif($extTable){
            $tables[$extTable] = $extTable;
        }

        // Where
        if ($table) $where[$table][] = $table . ".ClassName = '" . $className . "'";

        // Tag filter
        // Should be REGEX so we don't get partial matches
        if ($extTable) {
            foreach ($terms as $term) {
                $filter[$table][] = $extTable . ".Tags REGEXP '(^|,| )+" . Convert::raw2sql($term) . "($|,| )+'";
            }
        }


        // Build Query
        foreach($tables as $table){

            if (array_key_exists($table, $joins)){

                // Prepare Where Statement
                $uWhere     = array_unique($where[$table]);
                $uFilter    = array_unique($filter[$table]);

                // this lookupMode injection will prob break something in AND mode
                $wSql         = "(".implode(' OR ',$uWhere).") AND (".implode(' ' . $lookupMode . ' ',$uFilter).")";

                // Make the rest of the SQL
                if ($sql) $sql.= "UNION ALL"."\n\n";
                $rowCountSQL = !$sql ? "SQL_CALC_FOUND_ROWS " : "" ;
                $sql.= "SELECT " . $rowCountSQL . $table . ".ClassName, " . $table . ".ID" . "\n";
                $sql.= "FROM " . $table . "\n";

                // join
                $join = array_unique($joins[$table]);
                foreach($join as $j){
                    $sql .= " LEFT JOIN " . $j . " ON " . $table . ".ID = " . $j . ".ID" . "\n";
                }

                // Add the WHERE statement
                $sql .= "WHERE " . $wSql . "\n\n";
            }
        }

        // Add Global Filter to Query
        if ($filterSql) {
            $sql .= (count($tables) == 1 ? "AND " : "WHERE ") . $filterSql;
        }

        // Add Limits to Query
        $sql .= " LIMIT " . $start . "," . $limit;

        // Get Data
        // die($sql);
        $result = $db->query($sql);
        $result = $result ? $result->fetchAll(PDO::FETCH_OBJ) : array() ;

        // Convert to DOs
        foreach( $result as $entry ){

            // Make the data easier to work with
            $entry         = (object) $entry;
            $className     = $entry->ClassName;

            // this is faster but might not pull in relations
            //$dO = new $className;
            //$dO = DataObjectHelper::populate($dO, $entry);

            // this is slower, but will be more reliable
            $dO = DataObject::get_by_id($className, $entry->ID);

            $set->push($dO);
        }
        $set->unlimitedRowCount = $db->query('SELECT FOUND_ROWS() AS total')->fetch(PDO::FETCH_OBJ)->total;
        return $set;
    }
}

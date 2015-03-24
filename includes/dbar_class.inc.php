<?php
include 'dbpdo_class.inc.php';
$object_dbar = "dbar";

/**
 * dbactiverecord.inc.php
 * Class to manipulate SQL and wrap it to methods for easier usages
 * @author Hendro Wibowo (hendro@tiwule.net)
 * @since 05 December 2012
 * @version 1.0.6
 * 
 */

class DBAR extends DBPDO{
    var $_where = "";
    var $_fields = "*";
    var $_order_by = "";
	var $_group_by = "";
    var $_limit = "";
    var $_per_page = "";
    var $_like = "";
    var $_table = "";
    var $_join = "";
    var $_sql = "";
    var $_queries = "";
	var $_sql_all = "";
	var $_sql_paging = "";
	var $_filtering = true;
    
	/**
	 * Compile SELECT query
	 * @access private 
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
    function _build_query($limit=false) {
        $this->_sql  = "SELECT " . $this->_fields . " FROM " . $this->_table;
        
        if(is_array($this->_where)) {
            $wh = array();
            
            foreach($this->_where as $fields => $values) {
                $wh[] = $fields . " = '" . $values . "'";
            }
            
            $this->_where = implode(' AND ', $wh);
        }
        
        if($this->_where != '' OR $this->_like != '') 
        	$where = " WHERE ";
		else
			$where = "";
        
        $this->_sql .= $this->_join;
		$this->_sql_all = $this->_sql;
		$this->_sql .= $where;
        $this->_sql .= $this->_where;
        $this->_sql .= $this->_like;
        $this->_sql .= $this->_order_by;
		$this->_sql .= $this->_group_by;
		$this->_sql_paging = $this->_sql;
		$this->_sql .= $this->_limit;
        
        if(preg_match('/\sOR\s|\sAND\s/', $this->_sql)) $this->_sql = preg_replace(array('/\sOR\s*OR\s/','/\sAND\s*AND\s/','/\sWHERE\s*OR\s/','/\sWHERE\s*AND\s/'), array(' OR ', ' AND ',' WHERE ', ' WHERE '), $this->_sql, 1);
		
		if(preg_match('/\sOR\s|\sAND\s/', $this->_sql_all)) $this->_sql_all = preg_replace(array('/\sOR\s*OR\s/','/\sAND\s*AND\s/','/\sWHERE\s*OR\s/','/\sWHERE\s*AND\s/'), array(' OR ', ' AND ',' WHERE ', ' WHERE '), $this->_sql_all, 1);
		
		if(preg_match('/\sOR\s|\sAND\s/', $this->_sql_paging)) $this->_sql_paging = preg_replace(array('/\sOR\s*OR\s/','/\sAND\s*AND\s/','/\sWHERE\s*OR\s/','/\sWHERE\s*AND\s/'), array(' OR ', ' AND ',' WHERE ', ' WHERE '), $this->_sql_paging, 1);
        
        $this->_where = "";
        $this->_fields = "*";
        $this->_order_by = "";
		$this->_group_by = "";
        $this->_limit = "";
        $this->_like = "";
        $this->_table = "";
        $this->_join = "";
        
        return $this;
    }
    
	/**
	 * Get the result from compiled query that produced by _build_query() method
	 * @access public
	 * @param string $table <optional>
	 * @param mixed $where <optional>. Can be array or string.
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
    function get($table = '', $where = '') {
        if($table != '') $this->from($table);
        if($where != '') $this->where($where);

        $this->_build_query();
        $sql = $this->_sql;
        $this->_queries = $this->_sql;
        // Hapus sql setelah prepare
        $this->_sql = null;
        return $this->query($sql);
    }
    
	/**
	 * Compile fields from array or string for SELECT <fields> clause
	 * @access public
	 * @param mixed $fields <optional>. Can be array or string.
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
    function select($fields) {
        $the_fields = '';
        
        if($fields != '') {
            if(is_array($fields)) {
                if(count($fields[0]) == 2) {
                    foreach ($fields as $field_name => $field_as) {
                        $the_fields .= $field_name . " AS " . $field_as . ",";
                    }
                } else {
                    foreach ($fields as $field) {
                        $the_fields .= $field . ",";
                    }
                }
                
                $this->_fields = trim($the_fields, ',');
            } else {
                $this->_fields = $fields;
            }
        }
        
        return $this;        
    }
    
	/**
	 * Generate table name for SELECT <fields> FROM <table> query
	 * @access public
	 * @param string $table. The table name.
	 * @param mixed $alias <optional>. Alias of table. Useful for join operation.
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
    function from($table, $alias = '') {
        if($alias == '') {
            $this->_table = $table;
        } else {
            $this->_table = $table . " " . $alias;
        }
        
        return $this;
    }

	/**
	 * Generate WHERE clause
	 * @access public 
	 * @param array $where. Can be array or string.
	 * @param string $condition_keyword <Optional>. This parameter has value 'AND' or 'OR'. Default is 'AND'
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
    function where($where, $condition_keyword = 'AND') {
        $condition = "";
        
        if(is_array($where)) {
            foreach($where as $fields => $values) {
                $values = $this->filtering($values);
                if(preg_match("/(\s|<|>|!|=|is null|is not null)/i", $fields)) {
               		$condition .= " " . $condition_keyword . " " . $fields ." '" . $values . "'";
				} else {
	                $condition .= " " . $condition_keyword . " " . $fields ." = '" . $values . "'";
				}
            }
        } else {
            $condition = $where;
        }
        
        if($this->_where) {
       		$this->_where .= " " . $condition_keyword . " " . $condition;
		} else {
			$this->_where = $condition;
		}
            
        return $this;
    }
	
	function special_where($where, $condition_keyword = 'AND') {
        $condition = "";
        
        if(is_array($where)) {
            foreach($where as $fields => $values) {
                $values = $this->filtering($values);
                if(preg_match("/(\s|<|>|!|=|is null|is not null)/i", $fields)) {
               		$condition .= " " . $condition_keyword . " " . $fields ." '" . $values . "'";
				} else {
	                $condition .= " " . $condition_keyword . " " . $fields ." = '" . $values . "'";
				}
            }
        } else {
            $condition = $where;
        }
        
        if($this->_where) {
       		$this->_where .= " " . $condition_keyword . " " . $condition;
		} else {
			$this->_where =  $condition ;
		}
		
		$this->_where = "(".$condition .")" ;
            
        return $this;
    }
	
	/**
	 * Generate WHERE <fields> IN (<values>) clause
	 * @access public 
	 * @param string $field_name. The field name.
	 * @param array $values. The values of field.
	 * @param string $condition_keyword <Optional>. This parameter has value 'AND' or 'OR'. Default is 'AND'
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
	function where_in($field_name, $values, $condition_keyword = 'AND') {
        $the_values = array();
            
        foreach($values as $value) {
            $value = $this->filtering($value);
            $the_values[] = "'" . $value . "'";
        }
		
		if($this->_where) {
       		$this->_where .= " " . $condition_keyword . " " . $field_name . " IN (" . implode(',', $the_values) . ")";
		} else {
			$this->_where = $field_name . " IN (" . implode(',', $the_values) . ")";
		}
            
        return $this;
    }
	
	/**
	 * Generate WHERE <fields> NOT IN (<values>) clause
	 * @access public 
	 * @param string $field_name. The field name.
	 * @param array $values. The values of field.
	 * @param string $condition_keyword <Optional>. This parameter has value 'AND' or 'OR'. Default is 'AND'
	 * @return object $this. Return the class object to maintain chainability.
	 * 
	 */
	function where_not_in($field_name, $values, $condition_keyword = 'AND') {
        $the_values = array();
            
        foreach($values as $value) {
            $value = $this->filtering($value);
            $the_values[] = "'" . $value . "'";
        }
		
        if($this->_where) {
       		$this->_where .= " " . $condition_keyword . " " . $field_name . " NOT IN (" . implode(',', $the_values) . ")";
		} else {
			$this->_where = $field_name . " NOT IN (" . implode(',', $the_values) . ")";
		}
            
        return $this;
    }
    
    function like($like, $condition_keyword = 'AND', $prefix = 'BOTH') {
        $condition = "";
        
        if(is_array($like)) {
            $conditions = array();
            
            foreach ($like as $field => $value) {
                $value = $this->filtering($value);
                switch ($prefix) {                    
                    case 'BEFORE':
                        $value = "%" . $value;
                        break;
                        
                    case 'AFTER':
                        $value = $value . "%";
                        break;
                        
                    case 'BOTH':
                    default:
                        $value = "%" . $value . "%";
                        break;
                }
                
                $conditions[] = $field . " LIKE '" . $value . "'";
            }
            
            $condition = implode(' ' . $condition_keyword . ' ', $conditions);
        } else {
            $condition = $like;
        }
        
        $this->_like = " " . $condition_keyword . " " . $condition;
        
        return $this;
    }
	
	function special_like($like, $condition_keyword = 'AND', $prefix = 'BOTH') {
        $condition = "";
        
        if(is_array($like)) {
            $conditions = array();
            
            foreach ($like as $field => $value) {
                $value = $this->filtering($value);
                switch ($prefix) {                    
                    case 'BEFORE':
                        $value = "%" . $value;
                        break;
                        
                    case 'AFTER':
                        $value = $value . "%";
                        break;
                        
                    case 'BOTH':
                    default:
                        $value = "%" . $value . "%";
                        break;
                }
                
                $conditions[] = $field . " LIKE '" . $value . "'";
            }
            
            $condition = implode(' ' . $condition_keyword . ' ', $conditions);
        } else {
            $condition = $like;
        }
        
        $this->_like = " " . "AND" . " (" . $condition . ")";
		
        return $this;
    }
    
    function join($table, $on, $type = '') {
        $join_type = "";
        
        switch ($type) {
            case 'inner':
                $join_type = " INNER JOIN ";
                break;
                
            case 'left':
                $join_type = " LEFT JOIN ";
                break;
                
            case 'right':
                $join_type = " RIGHT JOIN ";
                break;
            
            default:
                $join_type = " JOIN ";
                break;
        }
        
        $this->_join .= $join_type . " " . $table . " ON " . $on . " ";
        
        return $this;
    }
    
    function limit($limit, $offset = '') {
		/* for paging perpage */
		$this->_per_page = $limit;
        if($offset == '') {
            $this->_limit = " LIMIT " . $limit . " ";
        } else {
            $this->_limit = " LIMIT " . $offset . ", " . $limit . " ";
        }
        
        return $this;
    }
    
    function order_by($fields, $order = 'ASC') {
        $the_fields = '';
        
        if(is_array($fields)) {
            $field_array = array();
            
            foreach ($fields as $field) {
                $field_array[] = $field;
            }
            
            $the_fields = implode(', ', $field_array);
        } else {
            $the_fields = $fields;
        }
        
        $this->_order_by = " ORDER BY " . $the_fields . " " . mb_strtoupper($order, "utf-8") . " ";
        
        return $this;
    }
	
	function group_by($fields, $order = 'ASC') {
        $the_fields = '';
        
        if(is_array($fields)) {
            $field_array = array();
            
            foreach ($fields as $field) {
                $field_array[] = $field;
            }
            
            $the_fields = implode(', ', $field_array);
        } else {
            $the_fields = $fields;
        }
        
        $this->_group_by = " GROUP BY " . $the_fields . " " . mb_strtoupper($order, "utf-8") . " ";
        
        return $this;
    }
    
    function insert_data($table, $data=array()) {
        if($table == '') return FALSE;
        if(!is_array($data)) return FALSE;
        
        $fields = "";
        $values = "";
        
        foreach ($data as $key => $value) {
            $fields .= "`" . $key . "`,";
            $values .= ":" . $key . ",";
			$data[$key] = $this->filtering($value);
        }
        
        $sql = "INSERT INTO `".$table."` (" . trim($fields, ',') . ") VALUE(" . trim($values, ',') . ")";
        return $this->query_data($sql,$data);
    }
    
    function update_data($table, $data=array(), $where) {
        if($table == '') return FALSE;
        if(!is_array($data)) return FALSE;
        
        $fields = array();
        
        foreach ($data as $key => $value) {
            $fields[] = "`" . trim($key) . "` = :" . trim($key);
			$data[$key] = $this->filtering($value);
        }

        if(is_array($where)) {
            $wh = array();
            
            foreach($where as $field => $values) {
                $wh[] = "`" . $field . "` = :" . $field;
                $data[$field] = $values;
            }
            
            $where = "WHERE " . implode(' AND ', $wh);
        }
        
        $sql = "UPDATE `".$table."` SET " . implode(', ', $fields) . " " . $where;
        return $this->query_data($sql,$data);
    }
    
    function delete_data($table, $where) {
        if(is_array($where)) {
            $wh = array();
            
            foreach($where as $fields => $values){
                $wh[] = $fields . " = '" . $values . "'";
            }
            
            $where = "WHERE " . implode(' AND ', $wh);
        }
        
        $this->_sql  = "DELETE FROM `".$table."` " . $where;
        $this->_sql .= $this->_limit;
        
        return $this->query($this->_sql);
    }
    
    function get_queries($type="query") {
		if($type=="count"){
			return $this->_sql_paging;
		}else{
			return $this->_queries;
		}
    }
	
	function num_rows($all="") {
		if($all=="paging"){
			$sth = $this->_db->prepare($this->_sql_paging);
			$sth->execute();
			return $sth->rowCount();
		}elseif($all=="all"){
			$sth = $this->_db->prepare($this->_sql_all);
			$sth->execute();
			return $sth->rowCount();
		}else{
			return	$this->_sth->rowCount();
		}
	}
	/* Paging Dinamic with dbar */
	function paging($pagename="",$num_link=3,$page=""){
		/* Set Attribute */
		$url = "";
		$num_rows = $this->num_rows(true);
		$page_amount = ceil($num_rows / $this->_per_page);
		/* Default */
		if($page==""){
			$page = isset($_GET['p']) ? $_GET['p'] : ""; 
		}
		if($pagename==""){
			$pagename = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		}
		// data get tetap ada
		unset($_GET['p']);
		$query = http_build_query($_GET);
		$query = $query != '' ? '&' . $query : '';
		
		if ($page == 0) $page = 1;
		$prev = $page - 1;	
		$next = $page + 1;		
		$lpm1 = $page_amount - 1;

		if($page_amount > 1)
		{	
			$url .= "<ul class='pagination m_3 alignright'>";
			//fisrt button
			if ($page > (1+$num_link))
				$url .= "<li><a href='$pagename?p=1$query'>&laquo;</a></li>";
			//previous button
			if ($page > 1)
				$url .= "<li class='prev'><a href='$pagename?p=$prev$query'>&lsaquo;</a></li>";
		
				// for ( $counter = 0; $counter <= $page_amount; $counter += 1) {
					for ($counter = $page-$num_link; $counter <= $page + $num_link; $counter++)
					{
						if(!($counter<=0)&&!($counter>$page_amount)){
							$current = ($page == $counter) ? "class='active'" : "";
							$url .= "<li $current><a href='$pagename?p=$counter$query'>";
							$url .= $counter;
							$url .= "</a></li>";
						}
					}
				// }
			//Next button
			if ($page < $page_amount)
				$url .= "<li class='next'><a href='$pagename?p=$next$query'>&rsaquo;</a></li>";
			//Last button
			if ($page < $page_amount - $num_link)
				$url .= "<li><a href='$pagename?p=$page_amount$query'>&raquo;</a></li>";
		
			$url .= "</ul>";
		}
		
		return $url;
	}
	
	
	function filtering($str){
		if($this->_filtering == true){
			return addslashes($str);
		}else{
			return $str;
		}
	}
}

if(isset($object_dbar)){
	$$object_dbar = new DBAR();
}
 
/**
 * End of file dbactiverecord.inc.php
 * 
 */
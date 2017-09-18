<?php

class db_class {

    public function open() {
        $con = mysqli_connect("localhost", "root", "", "sulsocial");
        return $con;
    }

    public function pdocon() {
        $db = new PDO("mysql:host=localhost;dbname=sulsocial;", "root", "");
        $db->exec('SET NAMES utf8');
        return $db;
    }

    function baseUrl($suffix = '') {
        $protocol = strpos($_SERVER['SERVER_SIGNATURE'], '443') !== false ? 'https://' : 'http://';
        $web_root = $protocol . $_SERVER['HTTP_HOST'] . "/" . "sulsocial/";
        $suffix = ltrim($suffix, '/');
        return $web_root . trim($suffix);
    }

    function LbaseUrl($suffix = '') {
        $protocol = strpos($_SERVER['SERVER_SIGNATURE'], '443') !== false ? 'https://' : 'http://';
        $web_root = $protocol . $_SERVER['HTTP_HOST'] . "/" . "sulsocial/";
        $suffix = ltrim($suffix, '/');
        return $web_root . trim($suffix);
    }

    public function close($con) {
        mysqli_close($con);
    }

    /**
     * Insert query for Object
     * @param type $object
     * @param type $object_array
     * @return string/Exception
     */
    function insert($object, $object_array) {
        $db = $this->pdocon();
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col;
                $bindfields .= ":" . $col;
                $bindfields .= ', ';
                $fields .= ', ';
            } else {
                $fields .= $col;
                $bindfields .= ":" . $col;
            }
            $ss++;
        }

        $sql = $db->prepare('
		  INSERT INTO ' . $object . ' (' . $fields . ')
		  VALUES (' . $bindfields . ')
		');

        foreach ($object_array as $col => $val) {
            $sql->bindValue(':' . $col, $val, PDO::PARAM_STR);
        }
        if ($sql->execute() == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function insertAndReturnID($object, $object_array) {
        $db = $this->pdocon();
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col;
                $bindfields .= ":" . $col;
                $bindfields .= ', ';
                $fields .= ', ';
            } else {
                $fields .= $col;
                $bindfields .= ":" . $col;
            }
            $ss++;
        }

        $sql = $db->prepare('
		  INSERT INTO ' . $object . ' (' . $fields . ')
		  VALUES (' . $bindfields . ')
		');

        foreach ($object_array as $col => $val) {
            $sql->bindValue(':' . $col, $val, PDO::PARAM_STR);
        }
        if ($sql->execute() == 1) {
            return $db->lastInsertId();
        } else {
            return 0;
        }
    }

    function lastid($object, $column) {
        $db = $this->pdocon();
        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' ORDER BY ' . $column . ' DESC LIMIT 1
		  ');

        //foreach ($object_array as $col => $val){ $sql->bindValue(':'.$col,$val, PDO::PARAM_STR); }		

        if ($sql->execute()) {
            return $sql->fetchAll(PDO::FETCH_OBJ);
        } else {
            return 0;
        }
    }

    /**
     * if the object is exists
     * @param type $object
     * @param type $object_array
     * @return int
     */
    function SelectAllByID_Multiple($object, $object_array) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' WHERE ' . $fields . '
		  ');
        $arrayparam = "";
        foreach ($object_array as $col => $val) {
            $arrayparam[] = $val;
        }

        if ($sql->execute($arrayparam)) {
            $datacount = $sql->rowCount();
            if ($datacount == 0) {
                return 0;
            } else {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            }
        } else {
            return 0;
        }
    }

    function exists_multiple($object, $object_array) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }


        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' WHERE ' . $fields . '
		  ');
        $arrayparam = "";
        foreach ($object_array as $col => $val) {
            $arrayparam[] = $val;
        }

        if ($sql->execute($arrayparam)) {
            return $sql->rowCount();
        } else {
            return 0;
        }
    }
    
function FlyQueryWithLimit($slysql,$position, $item_per_page, $st = 0) {
        $db = $this->pdocon();

        $sql = $db->prepare($slysql);
        
        $sql->bindParam(':start', $position, PDO::PARAM_INT);
        $sql->bindParam(':end',$item_per_page, PDO::PARAM_INT);
        
//        $sql->execute() or die(print_r($sql->errorInfo()));
//        $pictures = $sql->fetchAll(PDO::FETCH_ASSOC);
        if ($sql->execute()) {
            if ($st == 0) {
                $datacount = $sql->rowCount();
                if ($datacount == 0) {
                    return 0;
                } else {
                    return $sql->fetchAll(PDO::FETCH_OBJ);
                }
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    function FlyQuery($slysql, $st = 0) {
        $db = $this->pdocon();

        $sql = $db->prepare($slysql);

        if ($sql->execute()) {
            if ($st == 0) {
                $datacount = $sql->rowCount();
                if ($datacount == 0) {
                    return 0;
                } else {
                    return $sql->fetchAll(PDO::FETCH_OBJ);
                }
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

    function FlyQueryWithCond($object, $select, $object_array, $limit) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        if ($limit == 0) {
            $sql = $db->prepare('
			  select ' . $select . ' from (SELECT * FROM ' . $object . '   WHERE ' . $fields . ' ORDER by id DESC) ' . $object . ' ORDER BY id ASC
			  ');
        } else {
            $sql = $db->prepare('
			  select ' . $select . ' from (SELECT * FROM ' . $object . '   WHERE ' . $fields . ' ORDER by id DESC LIMIT ' . $limit . ') ' . $object . ' ORDER BY id ASC
			  ');
        }
        $arrayparam = "";
        foreach ($object_array as $col => $val) {
            $arrayparam[] = $val;
        }
        if ($sql->execute($arrayparam)) {
            $datacount = $sql->rowCount();
            if ($datacount == 0) {
                return 0;
            } else {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            }
        } else {
            return 0;
        }
    }

    function FlyQueryWithCondExists($object, $select, $object_array, $limit) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        if ($limit == 0) {
            $sql = $db->prepare('
			  select ' . $select . ' from (SELECT * FROM ' . $object . '   WHERE ' . $fields . ' ORDER by id DESC) ' . $object . ' ORDER BY id ASC
			  ');
        } else {
            $sql = $db->prepare('
			  select ' . $select . ' from (SELECT * FROM ' . $object . '   WHERE ' . $fields . ' ORDER by id DESC LIMIT ' . $limit . ') ' . $object . ' ORDER BY id ASC
			  ');
        }
        $arrayparam = "";
        foreach ($object_array as $col => $val) {
            $arrayparam[] = $val;
        }
        if ($sql->execute($arrayparam)) {
            return $datacount = $sql->rowCount();
        } else {
            return 0;
        }
    }

    function SelectAllByVal($object, $field, $fval, $fetch) {
        $count = 0;
        $fields = '';
        $db = $this->pdocon();
        $fields .= $field . "=:" . $field;

        $sql = $db->prepare('
		  SELECT ' . $fetch . ' FROM ' . $object . ' WHERE ' . $fields . '
		  ');

        $sql->bindParam(':' . $field, $fval);
        if ($sql->execute()) {
            $datacount = $sql->fetchColumn();
            return $datacount;
        } else {
            return 0;
        }
    }

    function SelectAllByVal2($object, $field, $fval, $field2, $fval2, $fetch) {
        $count = 0;
        $fields = '';
        $db = $this->pdocon();
        $fields .= $field . "=:" . $field;
        $fields .= " AND ";
        $fields .= $field2 . "=:" . $field2;
        $sql = $db->prepare('
		  SELECT ' . $fetch . ' FROM ' . $object . ' WHERE ' . $fields . '
		  ');

        $sql->bindParam(':' . $field, $fval);
        $sql->bindParam(':' . $field2, $fval2);

        if ($sql->execute()) {
            $datacount = $sql->fetchColumn();
            return $datacount;
        } else {
            return 0;
        }
    }

    function TotalRows($object) {
        $count = 0;
        $fields = '';
        $db = $this->pdocon();
        $sql = $db->prepare('
		  SELECT * FROM ' . $object . '
		  ');
        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            return $datacount;
        } else {
            return 0;
        }
    }

    /**
     * Select all the objects
     * @param type $object
     * @return array
     */
    function SelectAll($object) {
        $count = 0;
        $fields = '';
        $db = $this->pdocon();
        $sql = $db->prepare('
		  SELECT * FROM ' . $object . '
		  ');
        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            if ($datacount != 0) {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function SelectAllOrder($object, $order) {
        $db = $this->pdocon();
        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' ORDER BY id ' . $order . '
		  ');


        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            if ($datacount != 0) {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function SelectAll_ddate($object, $field, $startdate, $enddate) {
        $fields = '';
        $db = $this->pdocon();
        $fields .= $field . ">=:" . $field . "1";
        $fields .= " AND ";
        $fields .= $field . "<=:" . $field . "2";

        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' WHERE ' . $fields . '
		  ');

        $sql->bindParam(':' . $field . "1", $startdate);
        $sql->bindParam(':' . $field . "2", $enddate);


        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            if ($datacount != 0) {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    function SelectAllByIDOrderLimit($object, $object_array, $order, $limit) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=:" . $col;
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=:" . $col;
            }
            $ss++;
        }

        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' WHERE ' . $fields . ' ORDER BY id ' . $order . ' LIMIT ' . $limit . '
		  ');
        foreach ($object_array as $col => $val) {
            $sql->bindParam(':' . $col, $val);
        }
        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            if ($datacount == 0) {
                return 0;
            } else {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            }
        } else {
            return 0;
        }
    }

    function SelectAllLimit($object, $limit) {
        $db = $this->pdocon();
        $sql = $db->prepare('
		  SELECT * FROM ' . $object . ' LIMIT ' . $limit . '
		  ');


        if ($sql->execute()) {
            $datacount = $sql->rowCount();
            if ($datacount != 0) {
                return $sql->fetchAll(PDO::FETCH_OBJ);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Select object by ID
     * @param type $object
     * @param type $object_array
     * @return int
     */

    /**
     * Delete the object from database
     * @param type $object
     * @param type $object_array
     * @return string|\Exception
     */
    function delete($object, $object_array) {

        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ' AND ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        $sql = $db->prepare('
		  DELETE FROM ' . $object . ' WHERE ' . $fields . '
		  ');

        $arrayparam = "";
        foreach ($object_array as $col => $val) {
            $arrayparam[] = $val;
        }

        if ($sql->execute($arrayparam)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Delete the object
     * @param type $object
     * @param type $object_array
     */
    function update($object, $object_array) {
        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();
        $con_key_from_arr = array_keys($object_array);
        $key = $con_key_from_arr[0];
        $value = array_shift($object_array);
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ', ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        $sql = $db->prepare('
		  UPDATE ' . $object . ' 
		  	SET ' . $fields . ' 
				WHERE ' . $key . ' = ' . $value . '
		  ');

        $bindarray = '';
        foreach ($object_array as $col => $val) {
            $bindarray[] = $val;
        }

        if ($sql->execute($bindarray)) {
            return 1;
        } else {
            return 0;
        }
    }

    function updateUsingMultiple($object, $object_array, $object_array2) {
        $count = 0;
        $fields = '';
        $fields2 = '';
        $ss = 1;
        $sss = 1;
        $db = $this->pdocon();
        $count_col = count($object_array);
        foreach ($object_array as $col => $val) {
            if ($ss != $count_col) {
                $fields .= $col . "=?";
                $fields .= ', ';
            } else {
                $fields .= $col . "=?";
            }
            $ss++;
        }

        $count_col2 = count($object_array2);
        foreach ($object_array2 as $col2 => $val2) {
            if ($sss != $count_col2) {
                $fields2 .= $col2 . "=?";
                $fields2 .= ' AND ';
            } else {
                $fields2 .= $col2 . "=?";
            }
            $sss++;
        }


        $sql = $db->prepare('
		  UPDATE ' . $object . ' SET ' . $fields . ' WHERE ' . $fields2 . '
		  ');

        $allnewarray = array();
        foreach ($object_array as $col => $val) {
            //$sql->bindParam(':'.$col, $val); 
            $allnewarray[] = $val;
        }

        foreach ($object_array2 as $col2 => $val2) {
            //$sql->bindParam(':'.$col2, $val2);  
            $allnewarray[] = $val2;
        }

        if ($sql->execute($allnewarray)) {
            return 1;
        } else {
            return 0;
        }
    }

    function filename() {
        return basename($_SERVER['PHP_SELF']);
    }

    function amount_incre($object, $field, $amount, $cond1, $val1) {

        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();


        $sql = $db->prepare('
		  UPDATE ' . $object . ' 
		  	SET ' . $field . '=' . $field . '+' . $amount . ' 
				WHERE ' . $cond1 . '=' . $val1 . '
		  ');

        if ($sql->execute()) {
            return 1;
        } else {
            return 0;
        }
    }

    function amount_decre($object, $field, $amount, $cond1, $val1) {

        $count = 0;
        $fields = '';
        $bindfields = '';
        $ss = 1;
        $db = $this->pdocon();


        $sql = $db->prepare('
		  UPDATE ' . $object . ' 
		  	SET ' . $field . '=' . $field . '-' . $amount . ' 
				WHERE ' . $cond1 . '=' . $val1 . '
		  ');

        if ($sql->execute()) {
            return 1;
        } else {
            return 0;
        }
    }

    function limit_words($string, $word_limit) {
        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit)) . "...";
    }

    function dmy($month) {
        $chkj = strlen($month);
        if ($chkj == 1) {
            return $chkjval = "0" . $month;
        } else {
            return $chkjval = $month;
        }
    }

    function GenerateUniqueTransaction($session_id = '', $prefix = '', $st = '') {
        if ($st == 0) {
            //generate new session id
            if (empty($session_id)) {
                $_SESSION['SESS_' . $prefix] = session_id();
                return $_SESSION['SESS_' . $prefix];
            } else {
                return $_SESSION['SESS_' . $prefix];
            }
        } elseif ($st == 1) {
            //generate new session id
            $_SESSION['SESS_' . $prefix] = session_id();
            return $_SESSION['SESS_' . $prefix];
        }
    }

    
    
    
    
    public function emotion($comment) {
        $spdatacheck = $comment;
        $strrep_d = str_replace(":D", "<span><img src='images/icon/emotion/d_16.gif'></span>", $spdatacheck);
        $strrep_s = str_replace(":S", "<span><img src='images/icon/emotion/sad_16.gif'></span>", $strrep_d);
        $strrep_a = str_replace(":A", "<span><img src='images/icon/emotion/angry_16.gif'></span>", $strrep_s);
        $strrep_ldm = str_replace(":LDM", "<span><img src='images/icon/emotion/dumb_16.gif'></span>", $strrep_a);
        $strrep_love = str_replace(":LOVE", "<span><img src='images/icon/emotion/love_16.gif'></span>", $strrep_ldm);
        return $strrep_love;
    }

    public function duration($d1,$d2='') {
        date_default_timezone_set('Asia/Dhaka');
        $date1 = strtotime($d1);
        $date2 = time();
        $subTime = $date2 - $date1;
        $y = intval($subTime / (60 * 60 * 24 * 365));
        $d = intval($subTime / (60 * 60 * 24)) % 365;
        $h = intval($subTime / (60 * 60)) % 24;
        $m = intval($subTime / 60) % 60;

        //echo "Difference between ".date('Y-m-d H:i:s',$date1)." and ".date('Y-m-d H:i:s',$date2)." is:\n";
        if ($y != 0) {
            $result=(int) $y . " years ago.";
        } elseif ($d != 0) {
            $result=(int) $d . " days ago.";
        } elseif ($h != 0) {
            $result=(int) $h . " hours ago.";
        } elseif ($m != 0) {
            $result=(int) $m . " minutes ago.";
        } else {
            $result=" seconds ago.";
        }

        return $result;

    }
    
    
    
    
}

?>

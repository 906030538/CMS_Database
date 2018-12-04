<?php
	class Repository {
		const TABLE_NAME='undefined';
		private $PDO; // PDO Object
		function __construct($PDO = null) {
			global $conn;
			if (is_null($PDO))
				$this->PDO = $conn;
			else
				$this->PDO = $PDO;
		}
		private function fitInput(array $data) :array {
			global $db;
			switch ($db['type']) {
				case 'ODBC':
				case 'MSSQL':
					foreach($data as $k => $i) $data[$k] = iconv("GBK", "UTF-8", $i);
				default:
			}
			return $data;
		}
		private function fitOutput($data) {
			global $db;
			switch ($db['type']) {
				case 'ODBC':
				case 'MSSQL':
					switch (gettype($data)) {
						case 'string':
							$data = iconv("UTF-8", "GBK", $data);
							break;
						case 'object':
						case 'array':
							foreach($data as $k => $i)
								$data[$k] = $this->fitOutput($i);
						break;
						case 'boolean':
						case 'integer':
						case 'double':
						default:
					}
					break;
				default:
			}
			return $data;
		}
		private function initSearch(?b_query $search) :b_query {	// isDelete
			if (defined('SOFT_DELETE')) {
				if (is_null($search))
					$search = new b_query(new q_eq('IsDeleted', 0));
				else
					$search->AND(new q_eq('IsDeleted', 0), $search);
			} else if (is_null($search)) $search = new b_query(new q_no());
			return $search;
		}
		private function makeupcol(?array $colums) :string {
			if (is_null($colums)) return '*';
			$cols = '['.array_shift($colums).']';
			foreach ($colums as $col) {
				$cols .= ', ['.$col.']';
			}
			return $cols;
		}
		private function makeinsval(array $values) :string {
			$count = count($values);
			if ($count == 0) return '';
			$vals = '?';
			for ($i = 1; $i < $conunt; ++$i) {
				$vals .= ", ?";
			}
			return $vals;
		}
		private function makeupdval(array $values) :string {
			$val = array_shift($values);
			$vals = $val->__tostring();
			foreach ($values as $val) {
				$vals .= ', '.$val;
			}
			return $vals;
		}
		private function makeupseh(PDOStatement $rs, b_query $search) :PDOStatement {
			$array = $search->toValues();
			foreach ($array as $k => $i) {
				switch (gettype($i)) {
					case 'integer':
						$t = PDO::PARAM_INT;
						break;
					case 'string':
						$i = fitInput($i);
						$t = PDO::PARAM_STR;
						break;
					case 'object':
						$i = json_encode($i);
						$t = PDO::PARAM_STR;
						break;
					case 'boolean':
						$t = PDO::PARAM_BOOL;
						break;
					case 'double':
						$t = PDO::PARAM_INPUT_OUTPUT;
					default:
						$t = false;
				}
				$rs->bindValue($k + 1, $i, $t);
			}
			return $rs;
		}

		function select(array $colums = null, b_query $search = null) :array {
			$search = $this->initSearch($search);
			$sql = 'SELECT '.$this->makeupcol($colums).' FROM ['.$this::TABLE_NAME.']';
			$sql .= ' WHERE '.$search;
			$rs = $this->PDO->prepare($sql);
			$succ = $rs->execute($search->tovalues());
			if (!$succ) throw new Exception('Select '.$this::TABLE_NAME.' fail: '.$this->fitOutput($rs->errorInfo()[2]), QUERY_ERROR);
			return $this->fitOutput($rs->fetchAll());
		}
		function insert(array $colums, array $values) :boolean {
			$sql = 'INSERT ['.$this::TABLE_NAME.'] ('.$this->makeupcol($colums).') VALUES ('.$this->makeinsval($values).')';
			$rs = $this->PDO->prepare($sql);
			$succ = $rs->execute($values);
			if (!$succ) throw new Exception('Insert '.$this::TABLE_NAME.' fail: '.$this->fitOutput($rs->errorInfo()[2]), QUERY_ERROR);
			return $rs->rowCount();
		}
		function update(array $values, b_query $search = null) :int {
			$search = $this->initSearch($search);
			$sql = 'UPDATE ['.$this::TABLE_NAME.'] SET '.$this->makeupdval($values);
			$value = [];
			foreach($values as $i) $value = array_merge($value, $i->tovalues());
			if (!empty($search)) $sql .= ' WHERE '.$search;
			$rs = $this->PDO->prepare($sql);
			$succ = $rs->execute( array_merge($value, $search->tovalues()) );
			if (!$succ) throw new Exception('Update '.$this::TABLE_NAME.' fail: '.$this->fitOutput($rs->errorInfo()[2]), QUERY_ERROR);
			return $rs->rowCount();
		}
		function delete(b_query $search = null) :int {
			$search = $this->initSearch($search);
			$sql = 'DELETE FROM ['.$this::TABLE_NAME.']';
			if (!empty($search)) $sql .= ' WHERE '.$search;
			$rs = $this->PDO->prepare($sql);
			$succ = $rs->execute($search->tovalues());
			if (!$succ) throw new Exception('Delete '.$this::TABLE_NAME.' fail: '.$this->fitOutput($rs->errorInfo()[2]), QUERY_ERROR);
			return $rs->rowCount();
		}
		function error() {
			return $this->PDO->errorCode();
		}
	}
?>
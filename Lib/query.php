<?php
	abstract class q_bool {
		abstract function __toString();
		public $l;
		public $r;
		function __construct(string $l, string $r = null) {
			$this->l = $l;
			$this->r = $r;
		}
		function toValues() :array {
			return [$this->r];
		}
	}
	class q_no extends q_bool {
		function __construct() {
		}
		function __toString() {
			return '1 = ?';
		}
		function toValues() :array {
			return [1];
		}
	}
	class q_eq extends q_bool {
		function __toString() {
			return '['.$this->l.'] = ?';
		}
	}
	class q_ne extends q_bool {
		function __toString() {
			return '['.$this->l.'] <> ?';
		}
	}
	class q_gt extends q_bool {
		function __toString() {
			return '['.$this->l.'] > ?';
		}
	}
	class q_ge extends q_bool {
		function __toString() {
			return '['.$this->l.'] >= ?';
		}
	}
	class q_lt extends q_bool {
		function __toString() {
			return '['.$this->l.'] < ?';
		}
	}
	class q_le extends q_bool {
		function __toString() {
			return '['.$this->l.'] <= ?';
		}
	}
	class q_like extends q_bool {
		function __toString() {
			return '['.$this->l.'] Like ?';
		}
	}
	class b_bool extends q_bool {
		function __construct(q_bool $l, q_bool $r = null) {
			$this->l = $l;
			$this->r = $r;
		}
		function __toString() {
			return $this->l;
		}
		function toValues() :array {
			return array_merge($this->l->toValues(), $this->r->toValues());
		}
	}
	class b_AND extends b_bool {
		function __toString() {
			return $this->l.' AND '.$this->r;
		}
	}
	class b_OR extends b_bool {
		function __toString() {
			return '('.$this->l.' OR '.$this->r.')';
		}
	}
	class b_NOT extends b_bool {
		function __toString() {
			if (get_type($this->l) != "object" || get_class($this->l) == "b_bool")
				return 'NOT '.$this->l;
			return 'NOT ('.$this->l.')';
		}
	}
	class b_query extends b_bool {
		public $l, $r;
		function __construct(q_bool $l) {
			$this->l = $l;
		}
		function AND (q_bool $r) {
			$this->l = new b_AND ($this->l, $r);
		}
		function OR (q_bool $r) {
			$this->l = new b_OR ($this->l, $r);
		}
		function NOT () {
			$this->l = new b_NOT ($this->l);
		}
		function __toString() {
			return $this->l->__toString();
		}
		function toValues() :array {
			return $this->l->toValues();
		}
	}

	class v_eq extends q_eq {
	}
?>
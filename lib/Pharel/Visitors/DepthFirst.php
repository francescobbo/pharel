<?php

namespace Pharel\Visitors;

class DepthFirst extends Visitor {
    public $block;

    public function __construct($block = null) {
        if ($block)
            $this->block = $block;
        else
            $this->block = function($o){};

        parent::__construct();
    }

    public function visit($o) {
        parent::visit($o);
        $f = $this->block;
        return $f($o);
    }

    public function unary($o) {
        return $this->visit($o->expr);
    }

    public function binary($o) {
        $this->visit($o->left);
        return $this->visit($o->right);
    }

    public function _function($o) {
        $this->visit($o->expressions);
        $this->visit($o->alias);
        return $this->visit($o->distinct);
    }

    public function visit_Pharel_Nodes_NamedFunction($o) {
        $this->visit($o->name);
        $this->visit($o->expressions);
        $this->visit($o->distinct);
        return $this->visit($o->alias);
    }

    public function visit_Pharel_Nodes_count($o) {
        $this->visit($o->expressions);
        $this->visit($o->alias);
        return $this->visit($o->distinct);
    }

    public function nary($o) {
        foreach ($o->children as $child) {
            $this->visit($child);
        }

        return null;
    }

    public function visit_Pharel_Nodes_StringJoin($o) {
        return $this->visit($o->left);
    }

    public function visit_Pharel_Attribute($o) {
        $this->visit($o->relation);
        return $this->visit($o->name);
    }

    public function visit_Pharel_Table($o) {
        return $this->visit($o->name);
    }

    public function terminal($o) {
        return null;
    }

    public function visit_Pharel_Nodes_InsertStatement($o) {
        $this->visit($o->relation);
        $this->visit($o->columns);
        return $this->visit($o->values);
    }

    public function visit_Pharel_Nodes_SelectCore($o) {
        $this->visit($o->projections);
        $this->visit($o->source);
        $this->visit($o->wheres);
        $this->visit($o->groups);
        $this->visit($o->windows);
        return $this->visit($o->havings);
    }

    public function visit_Pharel_Nodes_SelectStatement($o) {
        $this->visit($o->cores);
        $this->visit($o->orders);
        $this->visit($o->limit);
        $this->visit($o->lock);
        return $this->visit($o->offset);
    }

    public function visit_Pharel_Nodes_UpdateStatement($o) {
        $this->visit($o->relation);
        $this->visit($o->values);
        $this->visit($o->wheres);
        $this->visit($o->orders);
        return $this->visit($o->limit);
    }

    public function visit_Array($o) {
        foreach ($o as $i)
            $this->visit($i);
        return null;
    }

    public function visit_Pharel_Nodes_And($o) {
        return $this->nary($o);
    }

	public function visit_Pharel_Nodes_Group($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Grouping($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Having($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Limit($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Not($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Offset($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_On($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Ordering($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Ascending($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Descending($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Top($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_UnqualifiedColumn($o) {
		return $this->unary($o);
	}

	public function visit_Pharel_Nodes_Avg($o) {
		return $this->_function($o);
	}

	public function visit_Pharel_Nodes_Exists($o) {
		return $this->_function($o);
	}

	public function visit_Pharel_Nodes_Max($o) {
		return $this->_function($o);
	}

	public function visit_Pharel_Nodes_Min($o) {
		return $this->_function($o);
	}

	public function visit_Pharel_Nodes_Sum($o) {
		return $this->_function($o);
	}

	public function visit_Pharel_Nodes_As($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Assignment($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Between($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_DeleteStatement($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_DoesNotMatch($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Equality($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_FullOuterJoin($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_GreaterThan($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_GreaterThanOrEqual($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_In($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_InfixOperation($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_JoinSource($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_InnerJoin($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_LessThan($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_LessThanOrEqual($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Matches($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_NotEqual($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_NotIn($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_NotRegexp($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Or($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_OuterJoin($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_Regexp($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_RightOuterJoin($o) {
		return $this->binary($o);
	}

	public function visit_Pharel_Nodes_TableAlias($o) {
		return $this->binary($o);
	}

  public function visit_Pharel_Nodes_Values($o) {
    return $this->binary($o);
  }

  public function visit_Pharel_Nodes_Union($o) {
    return $this->binary($o);
  }

	public function visit_Pharel_Attributes_Integer($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_Float($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_String($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_Time($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_Boolean($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_Attribute($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_Pharel_Attributes_Decimal($o) {
		return $this->visit_Pharel_Attribute($o);
	}

	public function visit_ActiveSupport_Multibyte_Chars($o) {
		return $this->terminal($o);
	}

	public function visit_ActiveSupport_StringInquirer($o) {
		return $this->terminal($o);
	}

	public function visit_Pharel_Nodes_Lock($o) {
		return $this->terminal($o);
	}

	public function visit_Pharel_Nodes_Node($o) {
		return $this->terminal($o);
	}

	public function visit_Pharel_Nodes_SqlLiteral($o) {
		return $this->terminal($o);
	}

	public function visit_Pharel_Nodes_BindParam($o) {
		return $this->terminal($o);
	}

  public function visit_Pharel_Nodes_Window($o) {
    return $this->terminal($o);
  }

  public function visit_Pharel_Nodes_True($o) {
    return $this->terminal($o);
  }

  public function visit_Pharel_Nodes_False($o) {
    return $this->terminal($o);
  }

	public function visit_BigDecimal($o) {
		return $this->terminal($o);
	}

	public function visit_Bignum($o) {
		return $this->terminal($o);
	}

	public function visit_Class($o) {
		return $this->terminal($o);
	}

	public function visit_Date($o) {
		return $this->terminal($o);
	}

	public function visit_DateTime($o) {
		return $this->terminal($o);
	}

	public function visit_FalseClass($o) {
		return $this->terminal($o);
	}

	public function visit_Fixnum($o) {
		return $this->terminal($o);
	}

	public function visit_Float($o) {
		return $this->terminal($o);
	}

	public function visit_NilClass($o) {
		return $this->terminal($o);
	}

	public function visit_String($o) {
		return $this->terminal($o);
	}

	public function visit_Symbol($o) {
		return $this->terminal($o);
	}

	public function visit_Time($o) {
		return $this->terminal($o);
	}

	public function visit_TrueClass($o) {
		return $this->terminal($o);
	}
}

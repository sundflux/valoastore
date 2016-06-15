<?php

namespace Webvaloa\Helpers;

use stdClass;

class Discountcodes
{
    private $db;
    private $code;
    private $codeobj;
    private $isValid;

    public function __construct($code)
    {
        $this->db = \Webvaloa\Webvaloa::DBConnection();
        $this->code = $code;

        $this->isValid = false;
        $this->validateCode($this->code);
    }

    public function validateCode($code)
    {
        $query = '
            SELECT *
            FROM babypanda_discount_code
            WHERE discount_code = ?
            AND used = 0';

        $stmt = $this->db->prepare($query);
        $stmt->set($this->code);

        try {
            $stmt->execute();

            $row = $stmt->fetch();

            if (!isset($row->id)) {
                $this->isValid = false;

                return;
            }

            $this->codeobj = new stdClass();
            $this->codeobj->code_id = $row->id;
            $this->codeobj->discount_code = $row->discount_code;
            $this->codeobj->discount_type = $row->discount_type;
            $this->codeobj->discount_value = $row->discount_value;

            $this->isValid = true;
        } catch (Exception $e) {
        }
    }

    public function isValid()
    {
        return $this->isValid;
    }

    public function isPercent()
    {
        if (isset($this->codeobj->discount_type) && $this->codeobj->discount_type == 'percent') {
            return true;
        }

        return false;
    }

    public function isEuro()
    {
        if (isset($this->codeobj->discount_type) && $this->codeobj->discount_type == 'euro') {
            return true;
        }

        return false;
    }

    public function getValue()
    {
        return $this->codeobj->discount_value;
    }

    public function markUsed($val = 1)
    {
        if (!$this->isValid) {
            return false;
        }

        if (!isset($this->codeobj->code_id) || !is_numeric($this->codeobj->code_id)) {
            return false;
        }

        $query = '
            UPDATE babypanda_discount_code
            SET used = ?
            WHERE id = ?
            AND discount_code = ?';

        $stmt = $this->db->prepare($query);
        $stmt->set($val);
        $stmt->set($this->codeobj->code_id);
        $stmt->set($this->code);

        try {
            $stmt->execute();

            return true;
        } catch (Exception $e) {
        }
    }

    public function markUnused($val = 0)
    {
        return $this->markUsed($val);
    }
}

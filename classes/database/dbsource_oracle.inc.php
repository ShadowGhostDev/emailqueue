<?php

namespace Emailqueue;

class dbsource_oracle extends dbsource
{
    public $uid;
    public $pwd;
    public $schema;

    public $statement;
    public $row;
    public $errors;

    public function __construct($uid, $pwd, $schema)
    {
        $this->dbsource('oracle');
        $this->uid = $uid;
        $this->pwd = $pwd;
        $this->schema = $schema;
    }

    public function connect()
    {
        $this->connectionid = OCILogon($this->uid, $this->pwd, $this->schema);

        return $this->connectionid;
    }

    public function disconnect()
    {
        return OCILogOff($this->connectionid);
    }

    public function query($sql)
    {
        $this->statement = @OCIParse($this->connectionid, $sql);
        @OCIExecute($this->statement, OCI_DEFAULT);
        $this->errors = OCIError($this->statement);
    }

    public function checkerrors()
    {
        if ($this->errors['code']) {
            return '[Código '.$this->errors['code'].'] '.$this->errors['message'];
        }

        return false;
    }

    public function fetchrow()
    {
        OCIFetchInto($this->statement, $results, OCI_ASSOC + OCI_RETURN_NULLS);
        $this->row = $results;

        return $results;
    }

    public function getfield($field)
    {
        return $this->row[$field];
    }

    public function countrows()
    {
        return OCIRowCount($this->statement);
    }

    public function getfield_date($string)
    {
        return strtotime($this->row[$string]);
    }
}

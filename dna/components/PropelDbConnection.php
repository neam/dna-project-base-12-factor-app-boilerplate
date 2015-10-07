<?php

class PropelDbConnection extends CDbConnection
{
    public function __construct($dsn = '', $username = '', $password = '')
    {
        $this->connectionString = 'mysql:set-by-propel';
        $this->username = 'set-by-propel';
        $this->password = 'set-by-propel';
    }

    protected function createPdoInstance()
    {
        return \Propel\Runtime\Propel::getWriteConnection('default')->getWrappedConnection();
    }
}

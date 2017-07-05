<?php
trait TesterMigrationUntils
{
    public function isAllMigrationsMade()
    {
        // get last migration version
        $migrations_paths = glob($this->migrations_path . '/*.php');
        $last_migration_path = array_pop($migrations_paths);
        $last_migration_to_migrate_version = $this->getVersionFromFilename($last_migration_path);

        // get last migration version from database
        $query = "SELECT version FROM schema_migrations ORDER BY version DESC LIMIT 1";
        $result = mysqli_query($this->db_connection, $query);
        $last_database_migration_version = mysqli_fetch_assoc($result)['version'];

        if ($last_migration_to_migrate_version == $last_database_migration_version) {
            return true;
        }

        return false;
    }

    public  function tablesList()
    {
        $query = $this->db_connection->prepare("SHOW TABLES");
        $query->execute();
        $result = $query->get_result();

        $tables = [];
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $value) {
                $tables[] = $value;
            }
        }
        $query->free_result();
        return $tables;
    }

    public  function clearTable($table_name)
    {
        // $query = $this->db_connection->prepare("TRUNCATE TABLE $table_name");
        // if ($query->execute()) {
        //     return true;
        // }
        // return false;

        $queries = [];
        $queries[] = "CREATE TABLE `" . $table_name . "_new` LIKE `" . $table_name . "`";
        $queries[] = "RENAME TABLE `" . $table_name . "` TO `" . $table_name . "_old`, `" . $table_name . "_new` TO `" . $table_name . "`";
        $queries[] = "DROP TABLE `" . $table_name . "_old`";

        foreach ($queries as $query) {
            $query_std = $this->db_connection->prepare($query);

            if (!$query_std->execute()) {
                return false;
            }
        }

        return true;
    }

    public  function clearDatabaseExceptSchema($tables)
    {
        // foreach ($tables as $table_name) {
        //     if (!$this->clearTable($table_name)) {
        //         throw new Exception('Can\'t clear table: '.$table_name);
        //     }
        // }

        $truncate_query = '';
        foreach ($tables as $table_name) {
            $result = $this->db_connection->query("SELECT * FROM $table_name");
            if ($result->num_rows > 0){
                $truncate_query .= "TRUNCATE TABLE `" . $table_name . "`;";
            }
        }

        $this->db_connection->multi_query($truncate_query;

        // handle results to avoid: Commands out of sync; you can't run this command now
        do {
            $this->db_connection->use_result();
        } while ($this->db_connection->more_results() && $this->db_connection->next_result());
    }

    public function checkIfTestDatabaseIsUpdated()
    {
        // check if test database is update to migration
        if (!$this->isAllMigrationsMade()) {
            die($this->display("\nMigrate tests database first.\n\n", 'FAILURE'));
        }
    }
}

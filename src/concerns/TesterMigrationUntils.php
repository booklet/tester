<?php
class TesterMigrationUntils
{
    public function isAllMigrationsMade() {
        // get last migration version
        $migrations_paths = glob($this->migrations_path);
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
}

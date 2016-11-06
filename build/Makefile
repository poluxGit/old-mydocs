# PHP-MyGED @ PoLuX - Makefile

all: begin clean cleanfiles gendb deploy
	@echo "";
	@echo "***********************************************";
	@echo "* Build OK 				      *";
	@echo "***********************************************";

begin:
	@echo "";
	@echo "***********************************************";
	@echo "* PHP-MyGED 				      *";
	@echo "***********************************************";
	@echo "";

gendb: gendb-app gendb-vault

gendb-app:
	@echo "";
	@echo "* Generating SQLite3 Database for Application.";
	@echo "------------------------------------------------";
	-sqlite3 app.db < ./databases/app-db/APP-DB_01_CreateTables.sql
	-sqlite3 app.db < ./databases/app-db/APP-DB_10_InsertData.sql

gendb-vault:
	@echo "* Generating SQLite3 Database for Vault.";
	@echo "------------------------------------------------";
	-sqlite3 vault.db < ./databases/vault-db/VLT-DB_01_CreateTables.sql

deploy: gendb
	@echo "";
	@echo "* Deploying databases files generated.";
	@echo "------------------------------------------------";
	-cp ./app.db ./../data/app_db/app.db
	@echo "* ApplicationDB file deployed successfully!";
	-cp ./vault.db ./../data/app_vault/db/vault.db
	@echo "* VaultDB file deployed successfully!";

cleanfiles:
	@echo "";
	@echo "* Cleaning files !"
	@echo "------------------------------------------------";
	@rm -f ./../data/app_vault/files/*
	@echo "-> Cleaning done!"

clean: begin
	@echo "";
	@echo "* Cleaning existing .db files!"
	@echo "------------------------------------------------";
	@rm -f ./*.db
	@echo "-> Cleaning done!"

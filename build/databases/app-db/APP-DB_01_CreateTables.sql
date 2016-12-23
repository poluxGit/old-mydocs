/*
 * Copyright 2016 polux.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Author:  polux
 * Created: 21 août 2016
 */

-- Création des tables

-- Table des Tiers
CREATE TABLE app_tiers
(
    tier_id VARCHAR(20) not null,
    tier_title VARCHAR(50) default 'Tier title - have to be define',
    tier_code VARCHAR(20) unique,
    tier_desc VARCHAR(4000),
    primary key (tier_id)
);

CREATE INDEX app_tiers_tier_title_idx on app_tiers
(
	tier_title
);

-- Table des Types de Document
CREATE TABLE app_typesdoc
(
	tdoc_id VARCHAR(20) not null,
	tdoc_title VARCHAR(50) default 'Type of document title - have to be define',
	tdoc_code VARCHAR(5) unique,
	tdoc_desc VARCHAR(4000),
	primary key (tdoc_id)
);
CREATE INDEX app_typesdoc_tdoc_title_idx on app_typesdoc
(
	tdoc_title
);


-- Table des Catégories
CREATE TABLE app_categories
(
	cat_id VARCHAR(20) not null,
	cat_title VARCHAR(50) default 'Categorie title - have to be define',
	cat_code VARCHAR(20) unique,
	cat_desc VARCHAR(4000),
	primary key (cat_id)
);
CREATE INDEX app_categories_cat_title_idx on app_categories
(
	cat_title
);

-- Table des Documents
CREATE TABLE app_documents
(
	doc_id VARCHAR(20) not null,
    tdoc_id VARCHAR(20) not null,
    doc_title VARCHAR(100) default 'Document title - have to be define',
	doc_code VARCHAR(50) unique,
    doc_desc VARCHAR(4000),
    doc_year VARCHAR(4) not null default '2016',
    doc_month VARCHAR(2) null,
    doc_day VARCHAR(2) null,
	primary key (doc_id),
    FOREIGN KEY(tdoc_id) REFERENCES app_typesdoc(tdoc_id)

);
CREATE INDEX app_documents_doc_title_idx on app_documents
(
	doc_title
);

-- Table de définition des meta-donnees par tdoc
CREATE TABLE app_meta_tdoc
(
    meta_id VARCHAR(20) not null,
    tdoc_id VARCHAR(20) not null,
    meta_title VARCHAR(100) default 'MetaDef title - have to be define',
    meta_desc VARCHAR(4000),
    meta_datatype VARCHAR(20),
    meta_pattern VARCHAR(100),
    meta_required VARCHAR(1) default 'N',
    meta_placeholder VARCHAR(100) default '',
    meta_mask VARCHAR(100) default '',
    meta_json_html_attributes VARCHAR(4000) default '{}',
    primary key (meta_id,tdoc_id),
    FOREIGN KEY(tdoc_id) REFERENCES app_typesdoc(tdoc_id)
);

CREATE INDEX app_meta_tdoc_meta_title_idx on app_meta_tdoc
(
	meta_title
);

-- Table de définition des meta-donnees par doc
CREATE TABLE app_meta_doc
(
    doc_id VARCHAR(50) not null,
    meta_id VARCHAR(20) not null,
    tdoc_id VARCHAR(20) default null,
    mdoc_title VARCHAR(100) default 'MetaInstance title - have to be define',
    mdoc_value VARCHAR(4000),
    primary key (doc_id,meta_id,tdoc_id),
    FOREIGN KEY (meta_id,tdoc_id) REFERENCES app_meta_tdoc(meta_id,tdoc_id),
    FOREIGN KEY(doc_id) REFERENCES app_documents(doc_id)
);
CREATE INDEX app_meta_doc_mdoc_title_idx on app_meta_doc
(
	mdoc_title
);

-- Table Association
CREATE TABLE app_asso_docs_tiers
(
    doc_id VARCHAR(20) not null,
    tier_id VARCHAR(20) not null,
    primary key (doc_id, tier_id),
    FOREIGN KEY(doc_id) REFERENCES app_documents(doc_id),
    FOREIGN KEY(tier_id) REFERENCES app_tiers(tier_id)
);

-- Table Association
CREATE TABLE app_asso_docs_cats
(
    doc_id VARCHAR(20) not null,
    cat_id VARCHAR(20) not null,
    primary key (doc_id, cat_id),
    FOREIGN KEY(doc_id) REFERENCES app_documents(doc_id),
    FOREIGN KEY(cat_id) REFERENCES app_categories(cat_id)
);

-- Table Association Docs <-> Files
CREATE TABLE app_asso_docs_files
(
    doc_id VARCHAR(20) not null,
    file_id VARCHAR(20) not null,
    primary key (doc_id, file_id),
    FOREIGN KEY(doc_id) REFERENCES app_documents(doc_id)
);


-- Table Fichiers
CREATE TABLE vault_files
(
    file_id VARCHAR(20) not null,
    file_path VARCHAR(4000) not null unique,
    file_originalname VARCHAR(500) default 'todef',
    file_size INT,
    file_mime VARCHAR(50),
    primary key (file_id)
);

-- Table Taches
CREATE TABLE tasks
(
    task_id VARCHAR(20) not null,
    task_title VARCHAR(4000) not null default '',
    task_start_timestamp INTEGER not null default 0,
    task_register_timestamp INTEGER not null default 0,
    task_end_timestamp INTEGER not null default 0,
    task_result_code INTEGER default null,
    task_status VARCHAR(50) default 'NEW',
    task_pid VARCHAR(50) default null,
    task_json_param VARCHAR(4000) default '',
    primary key (task_id)
);

-- Table Taches
CREATE TABLE tasks_log
(
    task_id VARCHAR(20) not null,
    task_log_description VARCHAR(4000) not null default '',
    task_log_timestamp INTEGER not null,
    task_status VARCHAR(50) default 'NEW',
    task_pid VARCHAR(50) default null,
    primary key (task_id,task_log_timestamp)
);

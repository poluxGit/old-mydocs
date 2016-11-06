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
 * Default Data - Data inserted after db structure generation
 *
 * Author:  polux
 * Created: 21 août 2016
 */

/* Categories */
INSERT INTO app_categories(cat_id,cat_title,cat_code,cat_desc) VALUES ('cat-administratif','Administratif','CAT-ADMIN','Documents administratifs.');

/* Tiers */
INSERT INTO app_tiers(tier_id,tier_title,tier_code,tier_desc) VALUES ('tie-edf','Electricité de France','EDF','Fournisseur d électricité');
INSERT INTO app_tiers(tier_id,tier_title,tier_code,tier_desc) VALUES ('tie-gdf','GDF Suez','GDF','Fournisseur de Gaz.');
INSERT INTO app_tiers(tier_id,tier_title,tier_code,tier_desc) VALUES ('tie-bouygues','Bouygues Telecom','BTCOM','FAI.');
INSERT INTO app_tiers(tier_id,tier_title,tier_code,tier_desc) VALUES ('tie-deficis','Agence Immo - DEFICIS','DEFICIS','Agence immobilière Nicolas DEFICIS.');

/* Type de Document */
INSERT INTO app_typesdoc(tdoc_id,tdoc_title,tdoc_code,tdoc_desc) VALUES ('tdoc-factures','Facture','FACT','Tous les types de factures.');
INSERT INTO app_typesdoc(tdoc_id,tdoc_title,tdoc_code,tdoc_desc) VALUES ('tdoc-bullpaies','Bulletin de paie','BPAI','Bulletins de salaires.');

/* Meta Type Doc */
INSERT INTO app_meta_tdoc(meta_id,tdoc_id,meta_title,meta_desc,meta_datatype,meta_pattern,meta_mask) VALUES ('mtdoc-bullpaies-01','tdoc-bullpaies','Montant BRUT','Montant BRUT du Salaire','number','[0-9]* \€','[0-9]* \€');
INSERT INTO app_meta_tdoc(meta_id,tdoc_id,meta_title,meta_desc,meta_datatype,meta_pattern) VALUES ('mtdoc-bullpaies-02','tdoc-bullpaies','Montant NET','Montant NET du Salaire','number','\€');
INSERT INTO app_meta_tdoc(meta_id,tdoc_id,meta_title,meta_desc,meta_datatype,meta_pattern) VALUES ('mtdoc-factures-01','tdoc-factures','Date','Date de Facturation.','date','aaaa-mm-dd');
INSERT INTO app_meta_tdoc(meta_id,tdoc_id,meta_title,meta_desc,meta_datatype,meta_pattern,meta_mask) VALUES ('mtdoc-factures-02','tdoc-factures','Montant T.T.C','Montant de la facture','number','[0-9] \€','[0-9] \€');

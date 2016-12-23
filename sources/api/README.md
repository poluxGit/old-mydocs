## PHP-MyGED - API Entrypoints

### Business Objects
+ document
+ categorie
+ tier
+ file
+ typedocument

### For 'document' Objects

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|GET|/api/v1/document/|None|Returns an array containing all documents main attributes.|
|GET|/api/v1/document/{id}|{id} : Document UID|Returns data concerning aimed Document.|
|POST|/api/v1/document/|| Create a new document.|
|POST|/api/v1/document/{docid}/file/|{docid} : Document UID <br/>POST Arg : FileToUpload (Form)| Upload a file and link it to the document.|
|POST|/api/v1/document/{docid}/file/{fileid}|{docid} : Document UID <br/>{fileid} : File UID| Link an existing file to the document.|
|DELETE|/api/v1/document/{docid}/file/{fileid}|{docid} : Document UID <br/>{fileid} : File UID| Delete a link between file and document.|


### For ' Type Document ' Objects (i.e: typedocument)

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|GET|/api/v1/typedocument/{id}|{id} : tdoc UID|Returns Attribute values about type of document.|
|GET|/api/v1/typedocument/{id}/getmeta/|{id} : tdoc UID|Returns Meta definition linked to a type of document.|



### For 'file' Objects

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|POST|/api/v1/file/|POST Arg : file (Form) |Register a new file into System and return her id.|
|GET|/api/v1/file/{id}|{id} : File UID|Returns a file content.|

### For 'tasks' Objects Management

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|GET|/api/v1/tasks/{id}|{id} : Task UID|Returns status of the OCR task.|


### For specifics 'OCR tasks' Objects

|HTTP <br/> Method|URI|Others <br/> Parameters|Description|
|:----------:|:---|:---:|:----:|
|POST|/api/v1/tasks/ocr/|None |Create a new OCR task and return her unique ID.|
|POST|/api/v1/tasks/ocr/{id}/{fileuid}|{id} : Task UID.<br/> {fileuid} : File UID targetted by OCR Task.|Launch OCR task on fileuid . |

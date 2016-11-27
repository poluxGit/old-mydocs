## MyDocs - My Personal documents management

```
Personal documents management & storage application
```

### All Functionnalities

- OCR File analysis .. .done!
- Store File and get contents easyly
- Manage file and set meta of document/files


### Application roadmap

|Version|Functionnality|Release Date|Description|
|:----------:|:---|:---:|:----:|
|alpha-01|OCR File analysis |None|OCR Analysis => Tesseract.|
|alpha-02|OCR File analysis integrated |None|OCR Analysis => Tesseract.|


###  Technical information

#### Dependencies

- [OCR Library **GITHUB Tesseract ocr for php** ](https://github.com/thiagoalessio/tesseract-ocr-for-php)
-

### Development informations

#### Dev. Environment



**Idées dev des tâches Asynchrone avec rappel du GUI**

```
Sauvegarde de l'objet dans $_SERVER ou $_SESSION

- Via listener PHP
- Voir avec EventHTTP::bind() pour mapper nouveau port relatifs à une tâche via apache

```

- Lien Listener : [ici](http://php.net/manual/fr/event.examples.php)
- Lien EventHTTP : [ici](http://php.net/manual/fr/eventhttp.bind.php)


    TODO Faire diagramme de séquence détaillé avant DEV ! IMPORTANT !


##### OCR - Commands

```bash
# pdfimages -> Extraction des images du PDF
pdfimages --png INPUT_PDF_FILE OUTPUT_IMG_PATTERN

# pdfseparate -> Sépare les pages d'un pdf
pdfseparate PDF -f Start -l End PDF_OUTPUT_PATTERN

# convert -> converti un fichier d'un format vers un autre
convert -density 300 -trim INPUT_PDF -quality 100 OUTPUT_IMG_WITH_EXT

# tesseract -> Analyse OCR d'une image
tesseract  toto.jpg -l fra output_txt
```

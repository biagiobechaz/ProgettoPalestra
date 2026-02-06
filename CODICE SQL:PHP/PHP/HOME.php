<html>
  <head>
    
    <style>
      table, tr, th, td {
        border: 1px solid black collapse;
      }
      .rigaIntestazione{
        background-color: red;
      }
      .rigaAlternata{
        background-color: lightblue;
      }
    </style>
  </head>
  <body>
    <h1>Tabella abbonamento</h1>
    <?php
      include("inc/datiConnessione.inc");
     
      try {
        include("inc/connessione.inc");
       
        ?>
        
       
    <form method="GET" action="#">
   
      <!-- filtra per genere -->
   
      <label for="id_abb">tipo: </label>
      <select name="ABBONAMENTO">
        <!-- &lt; corrisponde a '<' mentre &gt; corrisponde a '>' -->
        <option value="all">&lt; Tutti &gt;</option>
        <?php
            $sqlGeneri = "SELECT DISTINCT ABBONAMENTO FROM id_abb";
            $resultsGeneri = $conn->query($sqlGeneri);
            $generi = $resultsGeneri->fetchAll(PDO::FETCH_ASSOC);
            foreach($generi as $g) {
                echo "<option";
                if(isset($_GET["genere"]) && $_GET["genere"]==$g["Genere"])
                    echo " selected";
                echo ">$g[Genere]</option>";
            }
            $orderBy = false;
        ?>
      </select>
     
      <!-- ordina per -->
     
      <label for="ordinaPer">Ordina per: </label>
      <select name="ordinaPer">
        <option value="no">&lt; Nessun ordinamento &gt;</option>
        <option value="Titolo ASC"
        <?php
            if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="Titolo ASC"){
                echo "selected";
                $orderBy=true;
            }
        ?>>Titolo A-Z</option>
        <option value="Titolo DESC" <?php if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="Titolo DESC") { echo "selected"; $orderBy=true;}?>>Titolo Z-A</option>
        <option value="AnnoPubblicazione ASC" <?php if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="AnnoPubblicazione ASC") { echo "selected"; $orderBy=true;}?>>Anno dal più vecchio</option>
        <option value="AnnoPubblicazione DESC" <?php if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="AnnoPubblicazione DESC") { echo "selected"; $orderBy=true;}?>>Anno dal più recente</option>
        <option value="NumeroCopie DESC" <?php if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="NumeroCopie DESC") { echo "selected"; $orderBy=true;}?>>N. Copie dal più fornito</option>
        <option value="NumeroCopie ASC" <?php if(isset($_GET["ordinaPer"]) && $_GET["ordinaPer"]=="NumeroCopie ASC") { echo "selected"; $orderBy=true;}?>>N. Copie dal meno fornito</option>
      </select>
      <input type="submit" value="Invia"/>
    </form>
    <?php  
        // scrivo la query SQL in formato stringa
        $sql = "SELECT * FROM libro";
       
        // il metodo GET del form è quello che ci permette di
        // reperire le informazioni nell'array $_GET["informazione"];
        // cerco la variabile 'genere' perché il tag select aveva name='genere'
        if(isset($_GET["genere"]))
          // se è stato selezionato qualcosa di diverso da "< Tutti >" allora
          // inserirò la clausola WHERE nella query per il filtro
          if($_GET["genere"] != "all")
            $sql = $sql." WHERE genere='".$_GET["genere"]."'";
            // $sql .= " WHERE genere='$_GET[genere]'";
       
        if($orderBy)
            $sql .= " ORDER BY $_GET[ordinaPer]";
           
       
       
        //include("inc/stampaTabella.inc");
       
       
        // il metodo query() esegue il codice SQL, il metodo restituisce un DataSet
        $results = $conn->query($sql);
        // della classe DataSet esiste il metodo rowCount()
        echo "<h2>Sono presenti ".$results->rowCount()." libri</h2>";
        // stampo i tag per la Tabella
       
        //tag utili:
        //  table -> Tabella (che racchiude righe)
        //  tr    -> table row (riga che racchiude i dati)
        //  th    -> table header (intestazione)
        //  td    -> table data (dato normale)
       
        echo "<table>";
        echo "  <tr class='rigaIntestazione'>
                  <th>ISBN</th>
                  <th>Titolo</th>
                  <th>Anno Pubblicazione</th>
                  <th>Numero Copie</th>
                  <th>Genere</th>
                </tr>";
        // il metodo fetchAll() restituisce un array indicizzato di record. Ogni record è rappresentato da un array del tipo definito nel parametro (es: PDO::FETCH_ASSOC)
        // le chiavi dell'array associativo sono i nomi dei campi della tabella risultante dalla query
        $tab = $results->fetchAll(PDO::FETCH_ASSOC);
       
        // alternativa con il for classico
    //  for($i=0; $i<count($tab); $i++) {
    //    $riga = $tab[$i];
   
        $i = 0;
        // con un foreach scorro tutti i record della Tabella
        foreach($tab as $riga) {
          // stampo la riga con i dati
          echo "<tr ";
          // se è pari cambio lo sfondo in grigio chiaro
          if($i%2==0)
            echo "class='rigaAlternata'";
          // stampo i dati nella riga
          // all'ISBN metto un link ad una pagina "visualizzaLibro" a cui passo il parametro isbn del libro cliccato
          echo ">
                  <td>                  
                    <a href='visualizzaLibro.php?isbn=".$riga["ISBN"]."'>
                      ".$riga["ISBN"]."
                    </a>
                  </td>
                  <td>".$riga["Titolo"]."</td>
                  <td>".$riga["AnnoPubblicazione"]."</td>
                  <td>".$riga["NumeroCopie"]."</td>
                  <td>".$riga["Genere"]."</td>
                </tr>";
          // aggiorno il contatore
          $i++;
        }
        echo "</table>";
       
       
      // se saltano fuori exception legate alla connessione dal database le gestisco qui
      } catch(PDOException $e) {
          // stampando il messaggio di errore
          echo "<h2 style='color:red; font-weight:bold'>".$e->getMessage()."</h2>";
      }
    ?>
  </body>
</html>
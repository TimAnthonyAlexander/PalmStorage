# PalmStorage

## Allgemein

PalmStorage ist ein Datenbanken-Paradigma.
Es kann Daten speichern, Daten auslesen (anhand jedes Wertes) und Daten löschen.
Es gibt *keine* reservierten Spaltennamen.
Die Methoden haben keine Syntaxüberprüfung.

## Funktionen:

### Insert
```
(new palmstorage)->insert(database: "meinedatenbank", statement: "||benutzername°passwort||benutzername_123°benutzerpasswort123");
```

Dies trägt die Daten $benutzername und $passwort in die Spalten "benutzername" und "passwort" innerhalb der Datenbank "meinedatenbank".

Die Syntax für das Statement funktioniert so:

```ID||COL(°COL°COL°COL)||VAL(°VAL°VAL°VAL)```

Vergleichbare SQL Syntax:

```INSERT INTO `meinedatenbank` (COL, COL, COL, COL) VALUES (VAL, VAL, VAL, VAL)```

Die ID kann angegeben werden, und ersetzt vorhandene Werte.
Wenn nicht angegeben, wird die Datenbank mit automatischem Inkrement erweitert.
Die Anzahl der Spalten und Werte muss bei einem Schreibprozess immer gleich sein.
Innerhalb einer Datenbank zwischen Schreibprozessen kann sich die Spaltenanzahl ändern.

### Readval
```
$result = (new palmstorage)->readval(database: "meinedatenbank", statement: "benutzername||1");
```

Die Syntax für dieses Statement funktioniert so:

```COL||ID```

Vergleichbare SQL Syntax:

```SELECT `COL` FROM `meinedatenbank` WHERE `id`='ID'```

Für normales WHERE `XY`='AB' siehe *search*.

Beide Elemente müssen angegeben werden. Der Returntype ist String.

### Readvals
```
$userinfo_array = (new palmstorage)->readvals(database: "meinedatenbank", statement: "1");
```

Die Syntax für dieses Statement funktioniert folgendermaßen:

```ID```

Vergleichbare SQL Syntax:

```SELECT * FROM `meinedatenbank` WHERE `id`='ID'```

Es wird nur die ID angegeben. Ausgegeben wird die gesamte Spalte als Array, oder ein leerer Array, falls die Spalte nicht existiert.

### Search
```
$userpassword = (new palmstorage)->search(database: "meinedatenbank", statement: "passwort||benutzername||benutzername_123");
```
Dies gäbe das Passwort für den Benutzer "benutzername_123" aus.

Die Syntax für dieses Statement:

```WANTEDCOL||COL||VAL```

Vergleichbare SQL Syntax:

```SELECT `WANTEDCOL` FROM `meinedatenbank` WHERE `COL`='VAL'```


### Readdb
```
$userdatabase = (new palmstorage)->readdb(database: "meinedatenbank");
```

Dies gibt den gesamten Inhalt einer Datenbank.
Der Returntype ist vom Typ Array, und sieht so aus:

```
["1" => [
    "benutzername" => "benutzername_123",
    "passwort" => "benutzerpasswort123",
  ],
]
```

### Ungefährer Vergleich im Code (```(new connection)``` gibt ein PDO-Objekt zurück.)
#### PalmStorage vs MySSQL(PDO):

![vergleich](https://i.ibb.co/126r5qK/taa-screenshot-2021-01-22-at-14-15-12.png)

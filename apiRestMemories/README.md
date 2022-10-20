## Ma premier api REST en symfony

Memo : php bin\console doctrine:database:create

## Login

# Creer un compte

Le pseudo => taille minimum 5 caractéres et maximum 25 le pseudo est unique sans caractére spéciaux
Le mdp => taille minimum 4 caractéres et maximum 25 le mail est unique
Mail => valide (optionnel)
url : site/createlogin method POST
POST => pseudo : string, mdp : string mail: string
return => un json {
error : true / false,
(si error == true =>)
pseudo : "type error"
mdp : "type error"
mail : "type error"
}

# Se loger

url : site/login method POST
POST => pseudo : string mdp : string
return => si pseudo incorrect ou mdp incorect ou ...

## Liste de MEMORIES

# add List

url : site/addlist method POST
POST => listename : string
return => error ou true

# remove List

url : site/removelist method DELETE
DELETE => listename : string
return => error ou true

# update List

url : site/updatelist method PUT
PUT : listename : string , addCard : json
return => error ou true

# read List

url : site/getList/{nameList} method GET
return => list de cards ou false;

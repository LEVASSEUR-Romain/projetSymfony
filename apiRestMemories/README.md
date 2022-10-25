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
}

# Se loger

url : site/login method POST
POST => pseudo : string mdp : string
return => si pseudo incorrect ou mdp incorect ou ...

# se deconnecter

ulr : site/logout method GET

## Liste de MEMORIES

# add List

url : site/list-memory method POST
POST => nom : string , description : string| null
return => error ou statut : ok

# remove List

url : site/list-memory/{id} method DELETE
DELETE => listename : string
return => error ou statut : ok

# update List

url : site/list-memory/{id} method PUT
PUT : listename : string , addCard : json
return => error ou statut : ok

# read List

url : site/list-memory/{id} method GET
return => list de cards ou error;

# read All List

url : site/list-memory/all method GET
return => list de cards du user connecter ou error;

## Card memorie

# add Card

url : site/card method POST
POST => devant : string , derrière : string , devant_perso : string | null , derriére_perso: string | null
return => error ou statut : ok

# remove Card

url : site/card/{id} method DELETE
DELETE => listename : string
return => error ou statut : ok

# update Card

url : site/card/{id} method PUT
PUT : listename : string , addCard : json
return => error ou statut : ok

# read Card

url : site/card/{id} method GET
return => list de cards ou error;

# read All Card

url : site/list-memory/all method GET
return => list de cards du user connecter ou error;

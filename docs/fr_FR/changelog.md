# Changelog plugin EcoNetatmo

# 07/11/2025

- Correction problème en PHP8 lors du renouvellement des tokens
- Correction pour supprimer warning PHP lors de la synchronisation
- Déplacement de la documentation dans un repository github séparé afin de pouvoir mettre à jour la documentation sans générer un update du plugin
- Validation du plugin en Debian 12 Jeedom 4.5
  
# 09/09/2025

- Correction addresses internet de Netamo (remplacer .net par .com): il faudra certainement regénérer les tokens pour rétablir la communication 

# 20/05/2025

- Modification de la bibliothèque d'accès à Netatmo suite à un probleme qui empéchait la récupération des données

# 07/11/2024

- Passage des methodes cron en static pour éviter erreur en PHP 8

# 25/02/2024

- Mise à jour de la documentation 

# 21/07/2023

- Retrait du paramètre scope lors du rafraichissement du token 

# 20/07/2023

- Modification pour gérer l'authentification via authorization_code (voir documentation) 

# 26/05/2023

- Suppression lien paypal
- Modification des liens vers la documentation github
- Ajout liens vers la documentation beta

# 25/05/2023

- Chargement initial

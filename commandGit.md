# Aller sur la branche cible
git checkout develop

# Mettre la branche cible à jour
git pull origin develop

# Fusionner la branche source
git merge feature-xyz

# Résoudre les conflits si nécessaire, puis :
git add <fichiers_résolus>
git commit

# Pousser la branche fusionnée
git push origin develop

# Supprimer localement une branche
git branch -d feature-xyz

# Supprimer une branche distante
git push origin --delete feature-xyz

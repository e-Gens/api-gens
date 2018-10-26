#!/bin/sh
# Variáveis via Travis Secrets

# ==================================================
# sshpass => programa para entrada de senha no sftp
# -p [] => senha
# --------------------------------------------------
# ssh => conexão via ssh com o servidor
# -p [] => porta da conexão
# -v => (verbose) com log de execução
# -o => opção ssh - sem validação restrita por chaves
# --------------------------------------------------


# loga no ssh
if [ "${branch}" == "develop" ]; then
sshpass -p ${FTP_PASSWD} ssh -v -p ${FTP_PORT} -o StrictHostKeyChecking=no ${FTP_USER}@${FTP_HOST} <<END_SCRIPT
cd api-gens;
git pull;
composer install --no-interaction --no-dev --optimize-autoloader
END_SCRIPT
exit 0
fi
# ==================================================
# cd api-gens; => entra no diretório base da api
# git pull; => baixa as atualizações pelo git
# progress => exibe o progresso
# composer install ... => verifica deps e instala
# --------------------------------------------------


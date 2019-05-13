# Test

docker network create backend

cd test

export UID
export GID
docker-compose \
-f docker/all.yml \
-p yosmy_phone_verification \
up -d \
--remove-orphans --force-recreate

docker exec -it yosmy_phone_verification_php sh

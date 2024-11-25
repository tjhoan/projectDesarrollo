#!/bin/bash

HOST=$1
PORT=$2
TIMEOUT=${3:-15}

echo "Esperando a que $HOST:$PORT esté disponible por hasta $TIMEOUT segundos..."

for ((i=0; i<$TIMEOUT; i++)); do
    nc -z $HOST $PORT > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo "$HOST:$PORT está disponible."
        exit 0
    fi
    sleep 1
done

echo "Error: $HOST:$PORT no respondió dentro de $TIMEOUT segundos."
exit 1

FROM node:latest

WORKDIR /var/www/client
COPY package*.json ./
RUN npm install
COPY ./ .
RUN npm run build
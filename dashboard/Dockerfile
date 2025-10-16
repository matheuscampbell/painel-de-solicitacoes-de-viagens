# syntax=docker/dockerfile:1

FROM node:18-alpine

WORKDIR /app

COPY package.json yarn.lock ./
RUN yarn install

COPY . .

EXPOSE 5173
ENV HOST=0.0.0.0

CMD ["yarn", "dev", "--host", "0.0.0.0"]

FROM node:18

WORKDIR /app
# COPY src/backend/ ./
COPY src/ .
RUN mkdir public/uploads

RUN npm install

EXPOSE 3000
CMD ["node", "server.js"]

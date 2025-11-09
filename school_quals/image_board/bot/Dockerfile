FROM node:18

WORKDIR /app

COPY bot.js ./

# Install dependencies for Chromium
RUN apt-get update && apt-get install -y \
  wget \
  ca-certificates \
  fonts-liberation \
  libnss3 \
  libnspr4 \
  libxss1 \
  libasound2 \
  libatk-bridge2.0-0 \
  libgtk-3-0 \
  libx11-xcb1 \
  libxcomposite1 \
  libxdamage1 \
  libxrandr2 \
  libgbm1 \
  libpango-1.0-0 \
  libpangocairo-1.0-0 \
  libatspi2.0-0 \
  libdrm2 \
  --no-install-recommends && \
  apt-get clean && rm -rf /var/lib/apt/lists/*

RUN npm install puppeteer

CMD ["node", "bot.js"]

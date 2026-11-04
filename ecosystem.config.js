module.exports = {
  apps: [
    {
      name: 'wa-service',
      cwd: './wa-service',
      script: 'go',
      args: 'run main.go',
      autorestart: true,
      watch: false,
      max_memory_restart: '500M',
      env: {
        PORT: '8080',
        DB_PATH: './data/whatsapp.db',
      },
    },
  ],
};

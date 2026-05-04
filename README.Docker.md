# CorchoApp API — Docker

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop) instalado y corriendo

---

## Primera vez (setup inicial)

**1. Arranca los contenedores:**
```bash
docker compose up --build
```

**2. Genera el APP_KEY** (en otra pestaña del terminal):
```bash
docker compose exec server php artisan key:generate --show
```

Copia el resultado (algo como `base64:xxxxxxxx=`) y pégalo en `compose.yaml`:
```yaml
APP_KEY: base64:xxxxxxxx=
```

**3. Reinicia:**
```bash
docker compose down
docker compose up
```

La API estará disponible en `http://localhost:9000/api`.

---

## Arranque normal (después del setup)

```bash
docker compose up
```

Cada vez que arranca, el contenedor ejecuta automáticamente:
- `migrate:fresh --seed` — recrea las tablas y mete datos de prueba
- `passport:keys` — genera las claves OAuth

---

## Parar

```bash
docker compose down
```

---

## ⚠️ Importante: APP_KEY y GitHub

No subas el `APP_KEY` real a un repositorio público.  
Cada persona que clone el proyecto debe generar su propia clave (paso 2).

const express = require('express');
const { Pool } = require('pg');
const exifr = require('exifr');
const path = require('path');
const helmet = require('helmet');
const fs = require('fs');
const multer = require('multer');
const upload = multer({ dest: path.join(__dirname, 'public/uploads') });
const crypto = require('crypto');
const session = require('express-session');

// Session middleware — must come before routes

require('dotenv').config();

const app = express();
const pool = new Pool();

app.use(session({
  secret: 'your-secret-key',
  resave: false,
  saveUninitialized: false
}));

// Set CSP policy
app.use(
  helmet.contentSecurityPolicy({
    directives: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'", "'unsafe-inline'"], // adjust as needed
      styleSrc: ["'self'", "'unsafe-inline'"],
      imgSrc: ["'self'", "data:"],
      connectSrc: ["'self'"],
      fontSrc: ["'self'", "https:", "data:"],
      objectSrc: ["'none'"],
      upgradeInsecureRequests: [],
    },
  })
);

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// Main board
app.get('/', async (req, res) => {
  const posts = await pool.query("SELECT * FROM posts WHERE approved = true ORDER BY created_at DESC");
  res.render('index', { posts: posts.rows });
});

app.post('/submit', upload.single('image'), async (req, res) => {
    var { title, content } = req.body;
    try {
	title = title.replaceAll('"', '').replaceAll("'", '').replaceAll("`", '');
    }catch (err){
	title = "jantofix";
    }
    console.log(title);
    const imagePath = req.file ? `/uploads/${req.file.filename}` : null;
    let metadata = null;

    if (req.file && path.extname(req.file.originalname).toLowerCase() === '.jpg') {
      try {
	metadata = await exifr.parse(req.file.path);
	console.log(metadata);
      } catch (err) {
	console.error('Failed to parse EXIF data:', err.message);
      }
    }

    await pool.query(
    "INSERT INTO posts (title, content, image_path, metadata) VALUES ($1, $2, $3, $4)",
    [title, content, imagePath, metadata ? JSON.stringify(metadata.tags) : null]
    );

    res.redirect('/');
});

// Admin moderation
app.get('/admin', async (req, res) => {
    if (!req.session.admin) return res.redirect('/login');
    const posts = await pool.query("SELECT * FROM posts WHERE approved = false");
    res.render('admin', { posts: posts.rows });
});

app.post('/approve/:id', async (req, res) => {
  await pool.query("UPDATE posts SET approved = true WHERE id = $1", [req.params.id]);
  res.redirect('/admin');
});


app.get('/moderation/:hash', async (req, res) => {
  const { hash } = req.params;

  try {
    const result = await pool.query("SELECT * FROM posts WHERE approved = false");
    const posts = result.rows;
    const match = posts.find(post => {
      const titleHash = crypto.createHash('sha256').update(post.title).digest('hex').slice(0, 16);
      return titleHash === hash;
    });

    if (!match) return res.status(404).send('Post not found');

    res.send(`<h1>${match.title}</h1><p>${match.content}</p>`);
  } catch (err) {
    console.error('Error fetching post:', err.message);
    res.status(500).send('Server error');
  }
});

app.get('/login', (req, res) => {
  res.render('login');
});

app.post('/login', (req, res) => {
  const { username, password } = req.body;
  if (
    username === process.env.ADMIN_USER &&
    password === process.env.ADMIN_PASS
  ) {
    req.session.admin = true;
    return res.redirect('/admin');
  }
  res.send('Invalid credentials');
});

app.listen(3000, () => console.log('Server running on port 3000'));

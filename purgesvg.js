module.exports = {
  content: [
    './resources/views/app.blade.php',
  ],
  svgs: [
    {
      in: './node_modules/@fortawesome/fontawesome-free/sprites/*.svg',
      out: './public/images/fa.svg',
    },
    {
      in: './node_modules/feather-icons/dist/feather-sprite.svg',
      out: './public/images/feather.svg',
    },
  ],
  whitelist: {
    'regular.svg': [],
    'solid.svg': [],
    'feather-sprite.svg': [
      'home',
      'server',
      'sidebar',
      'users',
    ],
  },
};

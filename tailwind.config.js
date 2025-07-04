module.exports = {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                mono: {
                  blanco: '#f3fafe',
                  negro: '#141414',
                },
                bg: {
                  empty: '#1F1D2B',
                },
                universal:{
                  naranja: '#f05235',
                  naranja_opacity: '#fe502d36',
                  azul: '#4F959D',
                  azul_opacity: '#4f959d3a',
                  azul_secundaria: '#1c1b53'
                },
                semaforo: {
                  verde: '#1BCC75',
                  amarillo: '#FFA823',
                  rojo: '#FF3131',
                  verde_opacity: '#1bcc763f',
                  rojo_opacity : '#ff31312d'
                },
                secundary: {
                  default: '#1A2130',
                  light: '#A5B8D4',
                  opacity: 'rgba(26, 33, 48, 0.479)'
                }
            },
            boxShadow: {
              'universal-naranja': '0px 8px 20px #FE4F2D;',
            }
        },
    },
    plugins: [],
  }
  
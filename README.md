# redkram-woocommerce-prod-cat-tree
Dynamic Category Tree Menu for woocommerce - by Redkram
<br><br>Testeado con 1.555 categorías, a veces da Time Out al ejecutarlo pero estamos hablando de una e-commerce con 37.423 productos y 1.556 categorías (1 oculta).
<br><br>
El funcionamiento en si es sencillo ya que creamos un menú standard de ejemplo y le añadimos un nuevo elemento Tienda Online.
De este colgarán las diferentes categorías y subcategorías de los productos. 
<br>AVISO - Si estas están en más de 8 submenús de profundidad el resto se obviarán, dejando un menú con 8 elementos de submenú de profundidad.
<br>
<br>
Lógicamente construimos el menú y submenús partiendo de los términos con la taxonomía "product_cat" y referenciándola con su parent de forma recursiva. De esta manera obtenemos un árbol de categorías relacionadas entre si.
<br>
<br>
Este código lo puedes usar como te de la gana y modificarlo, ahora, si consigues que funciones mejor pues haces un commit y bienvenido sea.

<br><br>
Salu2
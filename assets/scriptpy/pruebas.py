import pandas as pd
import re

# Cargar el archivo Excel
file_path = 'sagrada madre.xls'  # Cambia esto con la ruta de tu archivo Excel
df = pd.read_excel(file_path)

# Crear nuevas columnas para Producto base y Variante
productos_base = []
variantes = []


# Función dinámica para separar producto base y variante
def separar_producto_y_variante(producto):
    producto = producto.strip()  # Eliminar espacios en blanco innecesarios

    # Partimos por detectar si hay un patrón de separación en el producto como '-' o '()'
    if '-' in producto or '(' in producto or ')' in producto:
        # Usamos regex para dividir por guiones o paréntesis
        partes = re.split(r'[-()]', producto)
        base = partes[0].strip()  # El producto base es la primera parte
        variante = partes[1].strip() if len(partes) > 1 else '-----'  # Variante o '-----'
        return base, variante

    # Si no hay guiones ni paréntesis, entonces dividimos por palabras
    palabras = producto.split()

    # Detectar un producto compuesto basado en la estructura de las palabras
    if len(palabras) > 3:  # Si hay más de 3 palabras, tratamos las primeras 3 como base
        base = " ".join(palabras[:3])  # Tomamos las primeras 3 palabras como el producto base
        variante = " ".join(palabras[3:]) if len(palabras) > 3 else '-----'
    else:
        base = " ".join(palabras[:2])  # Si hay menos de 3 palabras, usamos las primeras 2
        variante = palabras[2] if len(palabras) > 2 else '-----'  # Si hay más palabras, esa es la variante

    return base, variante


# Separar producto base y variante
for producto in df.iloc[:, 0]:  # Suponiendo que los datos están en la primera columna
    base, variante = separar_producto_y_variante(producto)
    productos_base.append(base)
    variantes.append(variante)

# Crear nuevo DataFrame con las columnas 'Producto' y 'Variante'
nuevo_df = pd.DataFrame({
    'Producto': productos_base,
    'Variante': variantes
})

# Guardar el resultado en un nuevo archivo Excel
output_path = 'ac.xlsx'  # Cambia esto con la ruta donde quieres guardar el archivo
nuevo_df.to_excel(output_path, index=False)

print("Archivo procesado y guardado correctamente.")

import sys
import json
import os
import pickle

# Verificar si se reciben argumentos
if len(sys.argv) < 2:
    print("No se han proporcionado datos.")
    sys.exit(1)

# Recibir los datos desde PHP
data = json.loads(sys.argv[1])
edad = data['edad']
peso = data['peso']
talla = data['talla']
hemoglobina = data['hemoglobina']

# Imprimir los datos recibidos para verificación
print(f"Datos recibidos: {data}")  # Verifica qué datos está recibiendo

# Definir la ruta absoluta al archivo modelo.pkl
model_path = os.path.join(os.path.dirname(__file__), 'modelo.pkl')

# Verificar si el archivo existe
if not os.path.exists(model_path):
    print(f"Error: No se encontró el archivo '{model_path}'")
    sys.exit(1)

# Cargar el modelo
with open(model_path, 'rb') as f:
    model = pickle.load(f)

# Realizar la predicción
prediccion = model.predict([[edad, peso, talla, hemoglobina]])  # Ajusta según la entrada del modelo

# Imprimir el resultado de la predicción
print(prediccion[0])  # Asegúrate de que el resultado se imprima correctamente

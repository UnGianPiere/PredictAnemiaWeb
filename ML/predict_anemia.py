import sys
import json
import numpy as np
import joblib

def cargar_modelo(ruta):
    """Carga el modelo desde la ruta especificada."""
    try:
        modelo = joblib.load(ruta)
        return modelo
    except Exception as e:
        print(f"Error al cargar el modelo: {e}")
        sys.exit(1)

def predecir(model, edad, hemoglobina, peso, talla):
    """Función para predecir el nivel de anemia basado en los parámetros de entrada."""
    # Crear la entrada para la predicción
    entrada = np.array([[int(edad)*12, float(hemoglobina) * 10.0, float(peso), float(talla)]])
    prediccion = model.predict(entrada)
    return prediccion[0]

def main():
    # Cargar el modelo utilizando la ruta absoluta
    model = cargar_modelo(r'C:\xampp\htdocs\ANEMIA_WEB\ML\modelo.pkl')

    # Verificar si se reciben argumentos
    if len(sys.argv) < 2:
        print("No se han proporcionado datos.")
        sys.exit(1)

    # Recibir los datos desde PHP
    try:
        input_data = sys.argv[1]
        data = json.loads(input_data)

        # Extraer y validar los datos
        edad = data['edad']
        hemoglobina = data['hemoglobina']
        peso = data['peso']
        talla = data['talla']

        # Llamar a la función de predicción
        resultado_anemia = predecir(model, edad, hemoglobina, peso, talla)

        # Devolver la predicción como JSON
        print(json.dumps({"resultado_anemia": int(resultado_anemia)}))

    except json.JSONDecodeError:
        print("Error al decodificar JSON.")
    except KeyError as e:
        print(f"Falta un campo en los datos: {e}")

if __name__ == "__main__":
    main()

import sys
import json
from ollama import AsyncClient
import asyncio

# Función principal que interactúa con el asistente
async def chat(message):
    # Crear el mensaje para enviar a Ollama
    messages = [
        {"role": "assistant", "content": "Eres un médico especialista en anemia con conocimientos en otras áreas, pero tu especialidad es el diagnóstico y tratamiento de la anemia. Ayuda al paciente a entender su tipo de anemia, resuelve dudas y ofrece recomendaciones prácticas y claras para combatirla. Siempre inicia preguntando sobre el tipo de anemia que tiene el paciente y luego ofrece consejos breves, como hábitos saludables y alimentos ricos en hierro. Limita tus respuestas a 30 palabras, excepto cuando el paciente haga preguntas que lo requieran. Los tipos de anemia comunes que te mencionarán son: sin anemia, anemia leve, anemia normal y anemia grave. Una vez que el paciente mencione su tipo de anemia, recuérdalo y continúa la conversación sin repetirlo innecesariamente."},
        {"role": "user", "content": message}
    ]
    
    assistant_response = ""
    
    # Enviar los mensajes a Ollama para obtener una respuesta
    async for part in await AsyncClient().chat(
        model="llama3", messages=messages, stream=True
    ):
        assistant_response += part["message"]["content"]
    
    # Devolver la respuesta en formato JSON
    return {"response": assistant_response}

# Si este script es ejecutado, procesamos el mensaje
if __name__ == "__main__":
    user_message = sys.argv[1]  # Tomamos el mensaje desde los argumentos
    response = asyncio.run(chat(user_message))  # Ejecutamos la función asíncrona
    print(json.dumps(response))  # Imprimimos la respuesta como JSON

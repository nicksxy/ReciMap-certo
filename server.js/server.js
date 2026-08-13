import express from "express";
import OpenAI from "openai";
import dotenv from "dotenv";

dotenv.config();

const app = express();
app.use(express.json({ limit: "10mb" }));

app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");
  res.setHeader("Access-Control-Allow-Methods", "POST, GET, OPTIONS");
  next();
});


const openai = new OpenAI({
  apiKey: process.env.OPENAI_API_KEY
});

app.use((req, res, next) => {
  res.setHeader("Access-Control-Allow-Origin", "*");
  res.setHeader("Access-Control-Allow-Headers", "Content-Type");
  next();
});

app.post("/openai", async (req, res) => {
  try {
    const { mensagem, imagem } = req.body;

    const content = [];

    if (mensagem) {
      content.push({ type: "text", text: mensagem });
    }

    if (imagem) {
      content.push({
        type: "image_url",
        image_url: { url: imagem }
      });
    }

    const response = await openai.responses.create({
      model: "gpt-4.1-mini",
      input: [
        {
          role: "user",
          content
        }
      ]
    });

    res.json({
      resposta: response.output_text
    });

  } catch (err) {
    res.status(500).json({ erro: err.message });
  }
});

app.listen(3000, () => {
  console.log("✅ Servidor rodando em http://localhost:3000");
});




const express = require('express')
const dotenv = require('dotenv')
const axios = require('axios')
dotenv.config()

const app = express()
const apiClient = axios.create({
    baseURL: process.env.API_URL,
})

app.get('/info', (req, res) => {
    res.json({
        version: '1',
        provider: 'test',
    })
})

app.get('/pipeline', (req, res) => {
    res.json([
        {
            $project: {
                _id: '$id',
                key: '$id',
                site_key: '$siteId',
                parent_key: '$parentId',
                model_key: '$modelId',
                manufacturer: {
                    $ifNull: ['$manufacturer', 'N/A'],
                },
                critical: {
                    $toBool: '$is_critical_system',
                },
                model: '$modelName',
                name: 1,
                description: 1,
            }
        }
    ])
})

app.post('/sync/:id', async (req, res) => {
    const { id } = req.params

    const data = require('./assets.json')

    await apiClient.post(`/data/${id}`, data.map(a => ({
        ...a,
        _id: a.id,
    })))

    res.status(201).send('')
})

app.listen(process.env.PORT || 3000)

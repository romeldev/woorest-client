<template>

    <div class="card">
        <div class="card-header">ApiTest.vue (API: {{urlService}})</div>

        <div class="card-body">
            <div class="row">
                <div class="col col-sm-6">
                    <div class="row">

                        <div class="col col-sm-12 form-group">
                            <label>*connections

                                <a href="#" @click="addConnection()"><b>+</b></a>
                            </label>
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>conn_type</th>
                                        <th>conn_name</th>
                                        <th>conn_target</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, key) in form.connections" :key="key">
                                        <td>
                                            <select v-model="item.conn_type"
                                                class="form-control form-control-sm">
                                                <option v-for="(type, key) in connTypes"
                                                    :key="key"
                                                    :value="type.value"
                                                    v-html="type.text"
                                                ></option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control form-control-sm"
                                                v-model="item.conn_name"
                                            >
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control form-control-sm"
                                                v-model="item.conn_target"
                                            >
                                        </td>
                                        <td>
                                            <a href="#" @click="removeConnection(key)" class="text-danger"><b>x</b></a>
                                        </td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>

                        <!-- content_type -->
                        <div class="col col-sm-12 form-group">
                            <label>*content_type: {{ content_type }}</label>
                            <select v-model="content_type" class="form-control form-control-sm">
                                <option v-for="(type, key) in contentTypes"
                                    :key="key"
                                    :value="type.value"
                                    v-html="type.text"
                                ></option>
                            </select>
                        </div>

                        <!-- content_source -->
                        <div class="col col-sm-12 form-group">
                            <label>*content_source: {{ content_source }}</label>
                            <select class="form-control form-control-sm"
                                v-model="content_source"
                                @change="handleContentSource"
                            >
                                <option v-for="(type, key) in contentSources"
                                    :key="key"
                                    :value="type.value"
                                    v-html="type.text"
                                ></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col col-sm-6">
                    <div class="row">
                        <!-- content file -->
                        <div class="col col-sm-12 form-group">
                            <label>*content_body (max:5MB):</label> <br>
                            <input
                                type="file"
                                ref="contentFile"
                                @change="handleFile"
                            >
                        </div>

                        <!-- content textarea -->
                        <div class="col col-sm-12 form-group">
                            <label>*content_body:</label>
                            <textarea
                                rows="5"
                                class="form-control"
                                v-model="content_body"
                            ></textarea>
                        </div>

                        <!-- content textarea -->
                        <div class="col col-sm-12 form-group">
                            <label>copy a content:</label>
                            <ul>
                                <li><a href="#" @click.prevent="copyContentBody(image64())">image64</a></li>
                                <li><a href="#" @click.prevent="copyContentBody('http://sources.test/ticket.png')">ticket.png</a></li>
                                <li><a href="#" @click.prevent="copyContentBody('http://sources.test/boleta.pdf')">boleta.pdf</a></li>
                                <li><a href="#" @click.prevent="copyContentBody('http://sources.test/texto.txt')">text.txt</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button class="btn btn-sm btn-primary" @click="submitTest()">Submit</button>

            <textarea class="form-control form-control-sm mt-3"  readonly rows="10" v-html="response"></textarea>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            contentTypes: [ 
                { value: 'image', text: 'IMAGEN' },
                { value: 'pdf', text: 'PDF' },
                { value: 'text', text: 'TEXTO' },
            ],

            contentSourcesAll: [ 
                { value: 'base64', text: 'BASE64' },
                { value: 'file', text: 'ARCHIVO' },
                { value: 'url', text: 'URL' },
                { value: 'string', text: 'STRING' },
            ],

            connTypes: [ 
                { value: 1, text: 'WINDOWS' },
                { value: 2, text: 'NETWORK' },
                { value: 3, text: 'FILE' },
            ],

            form: {
                connections: [
                    { conn_target: 'Caja', conn_type: 1, conn_name: 'Caja' },
                    { conn_target: 'Cocina', conn_type: 1, conn_name: 'Caja' },
                ],
            },

            content_type: 'image',
            content_source: 'base64',
            content_body: '',

            urlService: '/service/print',


            response: null,
        }
    },

    computed: {
        contentSources() {
            if( this.form.type==='text') {
                return this.contentSourcesAll
            }else {
                return this.contentSourcesAll.filter( i => i.value!='string')
            }
        }
    },

    mounted(){
    },

    methods: {
        addConnection() {
            this.form.connections.push({ print_in: '', conn_type: 1, conn_name: '' })
        },

        removeConnection(index) {
            this.form.connections.splice(index, 1)
        },

        copyContentBody( data ) {
            this.$refs.contentFile.value = null
            this.content_body = data
        },

        handleContentSource() {
            this.content_body = null
            this.$refs.contentFile.value = null
        },

        handleFile( e ) {
            this.content_body = e.target.files[0];
        },

        async submitTest() {
            try {
                this.response = null;

                let formData = new FormData();

                const connections = this.form.connections.map( item => {
                    return {
                        conn_type: item.conn_type,
                        conn_name: item.conn_name,
                        conn_target: item.conn_target,
                        content_type: this.content_type,
                        content_source: this.content_source,
                        content_body: this.content_body,
                    }
                });

                formData.append('connections', JSON.stringify(connections));
                const { data } = await window.axios.post(this.urlService, formData, 
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                )

                this.response = data
            } catch (error) {
                if( !error.response || (error.response && error.response.status!==422)){
                    this.$toast(error.message, 'error')
                }

                if( error.response && error.response.status==422){
                    let htmlErrors = ``
                    Object.keys(error.response.data).forEach( key => {
                        htmlErrors += `${error.response.data[key]}<br>`
                    })
                    this.$toast(htmlErrors, 'error')
                }
            }
        },


        image64() {
            return 'data:image/png;base64,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'
        },
    }
}
</script>

<style>

</style>
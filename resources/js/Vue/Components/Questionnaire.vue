<template>
<div>
    <form action="#"
          method="post" id="new-offer"
          enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4"><h6>Details</h6></div>
                <div class="col-sm-8">

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>* Name</label>
                                <input type="text"
                                       name="name"
                                       placeholder="Name"
                                       class="form-control"
                                       :value="questionnaire.name"
                                       required>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>* Titile</label>
                                <input type="text"
                                       name="title"
                                       placeholder="Title"
                                       class="form-control"
                                       :value="questionnaire.title"
                                       required>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>* Type</label>
                                <select class="custom-select"
                                        name="type_id"
                                        required
                                        v-model="selectedType"
                                >
                                    <option disabled selected value="">Select Questionnaire type
                                    </option>
                                    <option v-for="type in types" :value="type.id">@{{ type.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" id="answers" v-if="selectedTypeName != 'input'">
                            <button class="btn bg-danger-400 btn-sm rounded mb-2"
                                    @click.prevent="addAnswer()">
                                Add Answer <i class="fal fa-comments-question-check ml-1"></i>
                            </button>
                            <div class="form-group">
                                <label>* Answers</label>

                                <div class="input-group mb-3" v-for="(answer, key) in answers">
                                    <input type="text" v-model="answers[key]" name="answers[]"
                                           class="form-control">
                                    <div class="input-group-append" @click="removeAnswer(key)">
                                                    <span class="input-group-text text-danger">
                                                        <i class="icon-trash"></i>
                                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="active"
                                               class="custom-control-input" id="form-active"
                                               :checked="questionnaire.active"
                                        >
                                        <label class="custom-control-label"
                                               for="form-active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="text-right">
                    <input type="hidden" name="_token" :value="csrf">
                    <button class="btn bg-danger" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
</template>

<script>
export default {
    name: "Questionnaire",
    props: {
        questionnaire: Object,
        types: Array
    },
    data: function () {
        return {
            answers: [''],
            selectedType: '',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    },
    methods: {
        addAnswer() {
            this.answers.push('');
        },
        removeAnswer(key) {
            this.answers.splice(key, 1);
        },
        route (name) {
            return route(name);
        },
    },
    computed: {
        selectedTypeName() {
            let self = this;
            if (!self.selectedType) {
                return '';
            }

            return this.types.filter(function(val) {
                return val.id == self.selectedType;
            })[0].name;
        }
    },
    mounted() {
        this.answers = [];
        this.questionnaire.answers.forEach(answer => this.answers.push(answer.answer));
        this.selectedType = this.questionnaire.type_id;
    }
}
</script>

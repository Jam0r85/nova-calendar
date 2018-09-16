<template>
    <div>

        <!-- Event Details Form -->
        <div v-if="showingForm">

            <heading class="mb-6">
                {{__('Appointment Details')}}
            </heading>

            <card class="mb-6">

                <!-- Start -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <form-label>
                            {{__('Start')}}
                        </form-label>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <input
                            type="text"
                            v-model="start"
                            disabled
                            required
                            class="w-full form-control form-input form-input-bordered"
                        />
                    </div>
                </field-wrapper>

                <!-- Length -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <form-label>
                            {{__('Length')}}
                        </form-label>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <select class="w-full form-control form-select"
                            v-model="length"
                            v-on:change="setEnd">
                            <option value="15">15 Minutes</option>
                            <option value="30">30 Minutes</option>
                            <option value="45">45 Minutes</option>
                            <option value="60">1 Hour</option>
                            <option value="all">All Day</option>
                        </select>
                    </div>
                </field-wrapper>

                <!-- End -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <form-label>
                            {{__('End')}}
                        </form-label>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <input
                            type="text"
                            v-model="end"
                            disabled
                            required
                            class="w-full form-control form-input form-input-bordered"
                        />
                    </div>
                </field-wrapper>

                <!-- Title -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <form-label>
                            {{__('Title')}}
                        </form-label>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <input
                            type="text"
                            v-model="title"
                            required
                            class="w-full form-control form-input form-input-bordered"
                        />
                    </div>
                </field-wrapper>

                <!-- Description -->
                <field-wrapper>
                    <div class="w-1/5 px-8 py-6">
                        <form-label>
                            {{__('Description')}}
                        </form-label>
                    </div>
                    <div class="w-1/2 px-8 py-6">
                        <textarea
                            type="text"
                            v-model="description"
                            class="w-full form-control form-input form-input-bordered py-3 min-h-textarea"
                        />
                    </div>
                </field-wrapper>

                <div class="bg-30 flex px-8 py-4">
                    <button class="btn btn-default btn-danger mr-3" v-if="appointment_id" @click="deleteAppointment" >
                        Delete Appointment
                    </button>
                    <div class="flex-1 text-right">
                        <button class="btn btn-default btn-danger mr-3" @click="hideForm">
                            Cancel
                        </button>
                        <button class="btn btn-default btn-primary" @click="store">
                            {{ formLabel }} Appointment
                        </button>
                    </div>
                </div>

            </card>
        </div>

        <heading class="mb-6">{{__('Calendar')}}</heading>

        <card class="p-4">
            <full-calendar :config="config"
                :events="events"
                @day-click="dayClicked"
                @event-selected="eventClicked">
            </full-calendar>
        </card>

    </div>
</template>

<script>
import { FullCalendar } from 'vue-full-calendar';
import 'fullcalendar/dist/fullcalendar.css';

export default {
    components: {
        FullCalendar
    },

    data() {
        return {
            showingForm: false,
            formLabel: null,

            appointment_id: '',
            title: '',
            description: '',
            start: '',
            end: '',
            all_day: false,
            length: 15,

            events: [],
            config: {
                editable: false,
                selectHelper: false,
                hiddenDays: [ 0 ],
                slotDuration: '00:15:00',
                slotLabelInterval: '01:00:00',
                minTime: '08:00:00',
                maxTime: '20:00:00',
                businessHours: {
                    dow: [1, 2, 3, 4, 5],
                    start: '09:00',
                    end: '18:00'
                }
            }
        }
    },

    mounted() {
        this.loadAppointments();
    },

    methods: {

        loadAppointments() {
            this.getGeneral();
        },

        getGeneral() {
            Nova.request().get('/nova-vendor/nova-calendar/general').then(response => {
                this.events = response.data;
            })
        },

        showForm(label) {
            this.showingForm = true;
            this.formLabel = label;
            this.resetForm();
        },

        hideForm() {
            this.showingForm = false;
            this.resetForm()
        },

        resetForm() {
            this.title = '';
            this.description = '';
            this.start = '';
            this.length = 15;
        },

        setEnd() {
            this.all_day = false;

            if (this.length != 'all') {
                this.end = moment(this.start).add(this.length, 'm');
            } else {
                this.all_day = true;
                this.end = null;
            }
        },

        dayClicked(date, jsEvent, view) {
            if (! this.appointment_id) {
                this.showForm('Create');
            }

            this.start = date;

            this.setEnd();
        },

        eventClicked(event, jsEvent, view) {
            this.showForm('Update');
            this.appointment_id = event.id;

            Nova.request().post('/nova-vendor/nova-calendar/show', {
                appointment_id: this.appointment_id
            }).then(response => {
                this.start = response.data.start;
                this.end = response.data.end;
                this.title = response.data.title;
                this.description = response.data.description;
            });
        },

        store() {
            Nova.request().post('/nova-vendor/nova-calendar/store', {
                appointment_id: this.appointment_id,
                start: this.start,
                end: this.end,
                title: this.title,
                description: this.description,
                all_day: this.all_day
            }).then(response => {
                this.$toasted.show('Appointment stored.', { type: 'success' })
                this.hideForm();
                this.loadAppointments();
            }).catch(response => {
                this.$toasted.show('There was an error :(', {type: 'error'});
            });
        },

        deleteAppointment() {
            Nova.request().post('/nova-vendor/nova-calendar/delete', {
                appointment_id: this.appointment_id,
            }).then(response => {
                this.$toasted.show('Appointment deleted.', { type: 'success' })
                this.hideForm();
                this.loadAppointments();
            }).catch(response => {
                this.$toasted.show('There was an error :(', {type: 'error'});
            });
        }
    }
}
</script>

<style>
    
</style>

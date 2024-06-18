<template>
    <div class="w-100">
        <Qalendar
            :events="events"
            :config="config"
        />
    </div>
</template>

<script>
import { Qalendar } from "@skynetru/qalendar";

export default {
    name: "SessionsCalendar",
    props: {
        category: Object
    },
    components: {
        Qalendar
    },
    data: () => ({
        events: [],
        config: {
            week: {
                startsOn: 'monday',
                nDays: 7,
            },
            month: {
                showTrailingAndLeadingDates: true,
            },
            defaultMode: 'week',
            isSilent: true,
            showCurrentTime: false,
            styleMode: 'light',
        }
    }),
    methods: {
        loadEvents() {
            this.category.sessions.forEach((session) => {
                session.schedules.forEach((schedule) => {
                    schedule.bookings.forEach((booking) => {
                        let event = {
                            id: booking.id,
                            title: booking.first_name + ' ' + booking.last_name,
                            time: {
                                start: this.$moment(booking.date + ' ' + schedule.start).format('YYYY-MM-DD HH:mm'),
                                end: this.$moment(booking.date + ' ' + schedule.end).format('YYYY-MM-DD HH:mm')
                            },
                            colorScheme: '#' + session.color,
                            isEditable: false,
                        };

                        if (this.category.sessions[0].instructor) {
                            event.with = this.category.sessions[0].instructor.name
                        }

                        this.events.push(event)
                    });
                });

            })
        },
    },
    mounted() {
        this.loadEvents();
    }
}
</script>

<style>
@import "@skynetru/qalendar/dist/style.css";


</style>

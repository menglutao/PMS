<template>
  <div class="health-log">
    <h1 class="title">Health Log</h1>
    <div class="content">
      <section class="section cycle-history">
        <h2 class="subtitle">Cycle History</h2>
        <p>Records of historical period ...</p>
        <table class="cycle-table">
          <thead>
            <tr>
              <th>Starting Date</th>
              <th>End Date</th>
              <th>Cycle Length(days)</th>
              <th>Symptoms</th>
              <th>Intensity</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(cycle, index) in cycles" :key="index">
              <td>{{ cycle.start_date }}</td>
              <td>{{ cycle.end_date }}</td>
              <td>{{ cycle.cycle_length }}</td>
              <td>{{ cycle.symptom_type }}</td>
              <td>{{ cycle.intensity }}</td>
            </tr>
          </tbody>
        </table>
      </section>
      <section class="section predictions">
        <h2 class="subtitle">Next Menstruation Prediction</h2>
        <!-- <p>Display prediction of the next period arrival time here...</p> -->
        <p v-if="nextPeriodPrediction">{{ nextPeriodPrediction }}</p>
        <p v-else>Loading prediction...</p>
        <!-- Display the prediction data -->
      </section>
      <section class="section symptoms">
        <form @submit.prevent="submitMenstrualRecord">
          <h3>Are you experiencing menstruation right now?</h3>
          <h3>If so, let's write it down!</h3>
          <div class="date-inputs">
            <div class="date-field">
              <label for="start-date">Start Date:</label>
              <input
                type="date"
                id="start-date"
                v-model="menstrualRecord.startDate"
              />
            </div>
            <div class="date-field">
              <label for="end-date">End Date:</label>
              <input
                type="date"
                id="end-date"
                v-model="menstrualRecord.endDate"
              />
            </div>
          </div>

          <h3>Symptoms</h3>

          <div class="symptoms-list">
            <div v-for="symptom in symptoms" :key="symptom.symptom_id">
              <input
                type="checkbox"
                v-model="symptom.marked"
                :id="`symptom-${symptom.symptom_id}`"
              />
              <label :for="`symptom-${symptom.symptom_id}`">{{
                symptom.symptom_type
              }}</label>
            </div>
          </div>
          <div>
            <label for="intensity">Intensity:</label>
            <input
              type="number"
              id="intensity"
              v-model="menstrualRecord.intensity"
            />
          </div>
          <br />
          <button type="submit">Submit Symptoms</button>
        </form>
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "HealthLog",
  data() {
    return {
      cycles: [],
      userID: "",
      nextPeriodPrediction: "",
      symptoms: [],

      menstrualRecord: {
        startDate: "",
        endDate: "",
        symptoms: [],
        intensity: "",
      },
    };
  },
  created() {
    this.loadCycleData();
    this.loadSymptomData();
  },
  methods: {
    loadCycleData() {
      const user_name = this.$route.query.user_name;
      // console.log("user_name:", user_name);
      if (user_name) {
        const endpoint = `http://localhost:8000/cycle.php?user_name=${user_name}`;
        axios
          .get(endpoint)
          .then((response) => {
            console.log(response.data);
            if (response.data.success) {
              this.cycles = response.data.cycles;
              this.user_id = response.data.user_id;
              this.nextPeriodPrediction = response.data.prediction;
              console.log("cycle data:", this.cycles);
            } else {
              this.user_id = response.data.user_id;
              console.log("error message:", response.data.message);
            }
          })
          .catch((error) => {
            console.error("Error fetching cycle data:", error);
          });
      }
    },
    loadSymptomData() {
      const endpoint = "http://localhost:8000/symptom-list.php";
      axios
        .get(endpoint)
        .then((response) => {
          if (response.data.success) {
            this.symptoms = response.data.symptoms;
            console.log(this.symptoms);
          }
        })
        .catch((error) => {
          console.error("Error fetching cycle data:", error);
        });
    },
    submitMenstrualRecord() {
      const selectedSymptoms = this.symptoms
        .filter((symptom) => symptom.marked)
        .map((symptom) => symptom.symptom_id);
      console.log("selected symptoms:, userid", selectedSymptoms, this.user_id);
      const payload = {
        startDate: this.menstrualRecord.startDate,
        endDate: this.menstrualRecord.endDate,
        symptoms: selectedSymptoms,
        intensity: this.menstrualRecord.intensity,
        user_id: this.user_id,
      };
      const endpoint = "http://localhost:8000/saveMenstrualRecord.php";
      axios.post(endpoint, payload).then((response) => {
        if (response.data.success) {
          alert("Menstrual record and Symptoms saved successfully.");
          this.loadCycleData(); // Refresh cycle data
        } else {
          console.error("Failed to save data:", response.data.message);
        }
      });
    },
  },
};
</script>

<style scoped>
.health-log {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.title {
  text-align: center;
  margin-bottom: 40px;
}

.subtitle {
  margin-bottom: 20px;
}

.section {
  margin-bottom: 40px;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
}

.symptoms-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 10px;
}

.symptoms-list div {
  background: #f0f0f0;
  padding: 10px;
  border-radius: 4px;
  text-align: center;
}

.cycle-table {
  width: 100%;
  border-collapse: collapse;
}
.cycle-table th,
.cycle-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}
.cycle-table th {
  background-color: #f2f2f2;
}

.date-inputs {
  display: flex;
  justify-content: space-around; /* Adjust as necessary for your layout */
  align-items: center;
}

.date-field {
  margin: 0; /* Adjust as necessary for your layout */
  /* Add padding or other styling as needed */
}
</style>

<template>
  <div class="metric-card">
    <div class="metric-header">
      <div class="metric-icon" :style="{ backgroundColor: iconBgColor }">
        <component :is="icon" class="w-6 h-6" :style="{ color: iconColor }" />
      </div>
      <span class="metric-label">{{ label }}</span>
    </div>

    <div class="metric-content">
      <div class="metric-value">{{ formattedValue }}</div>
      <div v-if="trend" class="metric-trend" :class="trendClass">
        <svg v-if="trend > 0" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
        <span>{{ Math.abs(trend) }}%</span>
      </div>
    </div>

    <div v-if="subtitle" class="metric-subtitle">
      {{ subtitle }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomMetricCard',

  props: {
    label: {
      type: String,
      required: true,
    },
    value: {
      type: [Number, String],
      required: true,
    },
    prefix: {
      type: String,
      default: '',
    },
    suffix: {
      type: String,
      default: '',
    },
    trend: {
      type: Number,
      default: null,
    },
    subtitle: {
      type: String,
      default: null,
    },
    icon: {
      type: [String, Object],
      default: null,
    },
    iconColor: {
      type: String,
      default: '#1e40af',
    },
    iconBgColor: {
      type: String,
      default: '#eff6ff',
    },
    format: {
      type: String,
      default: 'number', // 'number', 'currency', 'percent'
    },
  },

  computed: {
    formattedValue() {
      let formatted = this.value;

      if (this.format === 'currency') {
        formatted = new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
        }).format(this.value);
      } else if (this.format === 'percent') {
        formatted = `${this.value}%`;
      } else if (this.format === 'number') {
        formatted = new Intl.NumberFormat('en-US').format(this.value);
      }

      return `${this.prefix}${formatted}${this.suffix}`;
    },

    trendClass() {
      if (!this.trend) return '';
      return this.trend > 0 ? 'up' : 'down';
    },
  },
};
</script>

<style scoped>
.metric-card {
  background: var(--bg-primary, white);
  border: 1px solid var(--border-color, #e2e8f0);
  border-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.25s ease;
}

.metric-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.metric-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.metric-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.metric-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text-secondary, #64748b);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.metric-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.metric-value {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--brand-primary, #1e40af);
  line-height: 1;
}

.metric-trend {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  font-weight: 600;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
}

.metric-trend.up {
  color: #059669;
  background: #d1fae5;
}

.metric-trend.down {
  color: #dc2626;
  background: #fee2e2;
}

.metric-subtitle {
  margin-top: 0.75rem;
  font-size: 0.875rem;
  color: var(--text-tertiary, #94a3b8);
}

@media (max-width: 768px) {
  .metric-value {
    font-size: 2rem;
  }
}
</style>

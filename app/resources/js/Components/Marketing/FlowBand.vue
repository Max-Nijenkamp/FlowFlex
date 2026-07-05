<script setup lang="ts">
import { flows as defaultFlows, type Flow } from '../../data/marketing'
import Reveal from '../UI/Reveal.vue'

const props = withDefaults(
    defineProps<{
        tag?: string
        title?: string
        lede?: string
        flows?: Flow[]
    }>(),
    {
        tag: '03',
        title: 'Data moves between departments on its own.',
        lede: "These aren't integrations you configure. They're how a single database behaves.",
        flows: undefined,
    },
)

const list = props.flows ?? defaultFlows
</script>

<template>
    <section class="ff-flow">
        <div class="ff-flow-glow"></div>
        <div class="ff-section relative" style="border-bottom: none">
            <div class="wrap">
                <Reveal>
                    <p class="ff-tag"><b>{{ tag }}</b> / FLOW</p>
                    <h2>{{ title }}</h2>
                    <p class="ff-lede">{{ lede }}</p>
                    <div class="ff-chain">
                        <div v-for="(f, i) in list" :key="f.event" class="ff-chain-row" :class="{ alt: i % 2 === 1 }">
                            <span class="route">{{ f.from }} → {{ f.to }}</span>
                            <span class="node"><span class="ff-node-dot"></span></span>
                            <span>
                                <span class="evt">{{ f.event }}</span>
                                <span class="fx">{{ f.effect }}</span>
                            </span>
                        </div>
                    </div>
                </Reveal>
            </div>
        </div>
    </section>
</template>

import React, { useEffect, useRef } from 'react';
import { ServerContext } from '@/state/server';
import { SocketEvent } from '@/components/server/events';
import useWebsocketEvent from '@/plugins/useWebsocketEvent';
import { Line } from 'react-chartjs-2';
import { useChart, useChartTickLabel } from '@/components/server/console/chart';
import { hexToRgba } from '@/lib/helpers';
import { bytesToString } from '@/lib/formatters';
import { theme } from 'twin.macro';
import ChartBlock from '@/components/server/console/ChartBlock';
import Tooltip from '@/components/elements/tooltip/Tooltip';
import styled, { keyframes } from 'styled-components';

// Animated pulse dot
const pulseAnim = keyframes`
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.3); }
`;

const NetBadgeContainer = styled.div`
    display: flex;
    align-items: center;
    gap: 12px;
`;

const NetBadge = styled.div<{ $color: string }>`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    background: ${p => p.$color === 'in' ? 'rgba(99,102,241,0.12)' : 'rgba(168,85,247,0.12)'};
    border: 1px solid ${p => p.$color === 'in' ? 'rgba(99,102,241,0.3)' : 'rgba(168,85,247,0.3)'};
    color: ${p => p.$color === 'in' ? '#818cf8' : '#c084fc'};
    transition: all 0.3s;
`;

const PulseDot = styled.span<{ $active?: boolean; $color: string }>`
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: ${p => p.$color === 'in' ? '#818cf8' : '#c084fc'};
    display: inline-block;
    animation: ${p => p.$active ? pulseAnim : 'none'} 1s ease-in-out infinite;
`;

export default () => {
    const status = ServerContext.useStoreState((state) => state.status.value);
    const limits = ServerContext.useStoreState((state) => state.server.data!.limits);
    const previous = useRef<Record<'tx' | 'rx', number>>({ tx: -1, rx: -1 });
    const lastRates = useRef<{ rx: number; tx: number }>({ rx: 0, tx: 0 });

    const cpu = useChartTickLabel('CPU', limits.cpu, '%', 2);
    const memory = useChartTickLabel('Memory', limits.memory, 'MiB');
    const network = useChart('Network', {
        sets: 2,
        options: {
            scales: {
                y: {
                    ticks: {
                        callback(value) {
                            return bytesToString(typeof value === 'string' ? parseInt(value, 10) : value) + '/s';
                        },
                    },
                },
            },
        },
        callback(opts, index) {
            return {
                ...opts,
                label: !index ? 'Inbound' : 'Outbound',
                borderColor: !index ? theme('colors.indigo.400') : theme('colors.purple.400'),
                backgroundColor: (context: any) => {
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, hexToRgba(!index ? theme('colors.indigo.500') : theme('colors.purple.500'), 0.4));
                    gradient.addColorStop(1, hexToRgba(!index ? theme('colors.indigo.500') : theme('colors.purple.500'), 0.0));
                    return gradient;
                },
                borderWidth: 2,
                tension: 0.4,
            };
        },
    });

    useEffect(() => {
        if (status === 'offline') {
            cpu.clear();
            memory.clear();
            network.clear();
            lastRates.current = { rx: 0, tx: 0 };
        }
    }, [status]);

    useWebsocketEvent(SocketEvent.STATS, (data: string) => {
        let values: any = {};
        try {
            values = JSON.parse(data);
        } catch (e) {
            return;
        }
        cpu.push(values.cpu_absolute);
        memory.push(Math.floor(values.memory_bytes / 1024 / 1024));

        const rxRate = previous.current.rx < 0 ? 0 : Math.max(0, values.network.rx_bytes - previous.current.rx);
        const txRate = previous.current.tx < 0 ? 0 : Math.max(0, values.network.tx_bytes - previous.current.tx);

        network.push([rxRate, txRate]);
        lastRates.current = { rx: rxRate, tx: txRate };

        previous.current = { tx: values.network.tx_bytes, rx: values.network.rx_bytes };
    });

    const isOnline = status === 'running' || status === 'starting';

    return (
        <>
            <ChartBlock title={'CPU Load'}>
                <Line {...cpu.props} />
            </ChartBlock>
            <ChartBlock title={'Memory'}>
                <Line {...memory.props} />
            </ChartBlock>
            <ChartBlock
                title={'Network I/O'}
                legend={
                    <NetBadgeContainer>
                        <Tooltip arrow content={'Inbound (Download)'}>
                            <NetBadge $color={'in'}>
                                <PulseDot $color={'in'} $active={isOnline} />
                                ↓ IN
                            </NetBadge>
                        </Tooltip>
                        <Tooltip arrow content={'Outbound (Upload)'}>
                            <NetBadge $color={'out'}>
                                <PulseDot $color={'out'} $active={isOnline} />
                                ↑ OUT
                            </NetBadge>
                        </Tooltip>
                    </NetBadgeContainer>
                }
            >
                <Line {...network.props} />
            </ChartBlock>
        </>
    );
};

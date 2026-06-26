import React from 'react';
import styled from 'styled-components';

interface ChartBlockProps {
    title: string;
    legend?: React.ReactNode;
    children: React.ReactNode;
}

const Container = styled.div`
    background: linear-gradient(135deg, rgba(15,23,42,0.8) 0%, rgba(30,27,75,0.6) 100%);
    border: 1px solid rgba(99,102,241,0.15);
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    transition: all 0.3s;

    &::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4);
        opacity: 0;
        transition: opacity 0.3s;
    }

    &:hover {
        border-color: rgba(99,102,241,0.3);
        box-shadow: 0 8px 24px rgba(99,102,241,0.1);
        &::before { opacity: 1; }
    }
`;

const Header = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(99,102,241,0.1);
    background: rgba(99,102,241,0.04);
`;

const Title = styled.h3`
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    margin: 0;
`;

const ChartArea = styled.div`
    padding: 8px 4px 4px;
`;

export default ({ title, legend, children }: ChartBlockProps) => (
    <Container>
        <Header>
            <Title>{title}</Title>
            {legend && <div style={{ display: 'flex', alignItems: 'center' }}>{legend}</div>}
        </Header>
        <ChartArea>{children}</ChartArea>
    </Container>
);

import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { IconDefinition } from '@fortawesome/free-solid-svg-icons';
import classNames from 'classnames';
import useFitText from 'use-fit-text';
import CopyOnClick from '@/components/elements/CopyOnClick';
import tw, { styled, css } from 'twin.macro';

interface StatBlockProps {
    title: string;
    copyOnClick?: string;
    color?: string | undefined;
    icon: IconDefinition;
    children: React.ReactNode;
    className?: string;
}

const StatContainer = styled.div`
    ${tw`relative flex flex-col justify-center rounded-2xl p-5 overflow-hidden transition-all duration-300`}
    background-color: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    
    &:hover {
        background-color: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(99, 102, 241, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.15);
    }
`;

const IconWrapper = styled.div<{ $color?: string }>`
    ${tw`absolute right-[-10px] top-[-10px] opacity-[0.15] transition-all duration-300`}
    svg {
        ${tw`w-24 h-24`}
        ${props => {
            if (props.$color?.includes('red')) return tw`text-red-500`;
            if (props.$color?.includes('yellow')) return tw`text-yellow-500`;
            if (props.$color?.includes('green')) return tw`text-green-500`;
            return tw`text-indigo-500`;
        }}
    }
    
    ${StatContainer}:hover & {
        ${tw`opacity-30 right-[0px] top-[0px]`}
        filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.3));
    }
`;

const ContentWrapper = styled.div`
    ${tw`relative z-10 flex flex-col`}
`;

const Title = styled.p`
    ${tw`text-[11px] font-black uppercase tracking-widest text-gray-400 mb-1`}
`;

const Value = styled.div`
    ${tw`font-bold text-gray-50 flex items-baseline`}
    text-shadow: 0 0 20px rgba(255,255,255,0.2);
`;

export default ({ title, copyOnClick, icon, color, className, children }: StatBlockProps) => {
    const { fontSize, ref } = useFitText({ minFontSize: 12, maxFontSize: 200 });

    return (
        <CopyOnClick text={copyOnClick}>
            <StatContainer className={className}>
                <IconWrapper $color={color}>
                    <FontAwesomeIcon icon={icon} />
                </IconWrapper>
                <ContentWrapper>
                    <Title>{title}</Title>
                    <Value ref={ref} className={'h-[2rem] truncate'} style={{ fontSize }}>
                        {children}
                    </Value>
                </ContentWrapper>
            </StatContainer>
        </CopyOnClick>
    );
};

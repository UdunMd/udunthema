import React, { forwardRef } from 'react';
import { Form } from 'formik';
import styled from 'styled-components/macro';
import FlashMessageRender from '@/components/FlashMessageRender';
import tw from 'twin.macro';
import { motion } from 'framer-motion';

type Props = React.DetailedHTMLProps<React.FormHTMLAttributes<HTMLFormElement>, HTMLFormElement> & {
    title?: string;
};

const SplitLayout = styled.div`
    ${tw`min-h-screen flex w-full bg-[#050505] overflow-hidden text-gray-100`}
`;

const LeftPanel = styled.div`
    ${tw`hidden lg:flex lg:flex-1 relative flex-col justify-center items-start p-20`}
    background: radial-gradient(circle at top left, rgba(79, 70, 229, 0.25), transparent 50%),
                radial-gradient(circle at bottom right, rgba(139, 92, 246, 0.2), transparent 50%),
                #0a0a0c;
    border-right: 1px solid rgba(255,255,255,0.05);

    &::after {
        content: '';
        ${tw`absolute inset-0 opacity-[0.03] pointer-events-none`}
        background-image: linear-gradient(#fff 1px, transparent 1px),
                          linear-gradient(90deg, #fff 1px, transparent 1px);
        background-size: 30px 30px;
    }
`;

const RightPanel = styled.div`
    ${tw`flex-1 lg:flex-none lg:w-[35rem] flex flex-col justify-center items-center p-8 md:p-16 relative`}
    background: #09090b;
`;

const BrandTitle = styled(motion.h1)`
    ${tw`text-5xl lg:text-7xl font-black tracking-tighter text-white mb-6 leading-tight relative z-10`}
    span {
        ${tw`text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-500`}
    }
`;

const BrandSlogan = styled(motion.p)`
    ${tw`text-xl text-gray-400 font-medium max-w-lg relative z-10 leading-relaxed`}
`;

const FormWrapper = styled(motion.div)`
    ${tw`w-full max-w-sm`}
`;

export default forwardRef<HTMLFormElement, Props>(({ title, ...props }, ref) => (
    <SplitLayout>
        <LeftPanel>
            <BrandTitle
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, ease: "easeOut" }}
            >
                AlxZen <span>Panel</span>
            </BrandTitle>
            <BrandSlogan
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: 0.2, ease: "easeOut" }}
            >
                Elevate Your Infrastructure. Deploy, manage, and scale your game servers with enterprise-grade control.
            </BrandSlogan>
        </LeftPanel>

        <RightPanel>
            <FormWrapper
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, ease: "easeOut" }}
            >
                <div css={tw`mb-10 text-center lg:text-left`}>
                    <img src={'/assets/svgs/pterodactyl.svg'} css={tw`block w-16 mb-6 mx-auto lg:mx-0`} alt="Logo" />
                    {title && <h2 css={tw`text-3xl font-bold tracking-tight text-white`}>{title}</h2>}
                    <p css={tw`text-gray-400 mt-2 text-sm`}>Welcome back! Please enter your details.</p>
                </div>

                <FlashMessageRender css={tw`mb-6`} />

                <Form {...props} ref={ref}>
                    {props.children}
                </Form>

                <p css={tw`text-center text-gray-500 text-xs mt-12`}>
                    &copy; 2015 - {new Date().getFullYear()}&nbsp;
                    <a
                        rel={'noopener nofollow noreferrer'}
                        href={'https://github.com/alxzy-group/alxzen'}
                        target={'_blank'}
                        css={tw`no-underline text-indigo-400 hover:text-indigo-300 transition-colors`}
                    >
                        alxzen Software
                    </a>
                </p>
            </FormWrapper>
        </RightPanel>
    </SplitLayout>
));

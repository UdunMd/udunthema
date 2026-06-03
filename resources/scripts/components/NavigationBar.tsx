import * as React from 'react';
import { useState, useEffect } from 'react';
import { Link, NavLink, useLocation, useRouteMatch } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
    faBars, faTimes, faServer, faCogs, faUserCircle, faSignOutAlt, 
    faTerminal, faFolderOpen, faDatabase, faCalendarAlt, faUsers, 
    faNetworkWired, faPlayCircle, faBoxOpen, faCloudDownloadAlt,
    faKey, faHistory, faUnlockAlt
} from '@fortawesome/free-solid-svg-icons';
import { useStoreState } from 'easy-peasy';
import { ApplicationStore } from '@/state';
import SearchContainer from '@/components/dashboard/search/SearchContainer';
import tw, { styled, css } from 'twin.macro';
import { faChevronDown, faChevronRight } from '@fortawesome/free-solid-svg-icons';
import http from '@/api/http';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import Avatar from '@/components/Avatar';

const NavContainer = styled.div`
    ${tw`fixed top-0 left-0 right-0 z-50 border-b border-white/5 h-20`}
    background-color: rgba(9, 9, 11, 0.85);
    backdrop-filter: blur(16px);
    transition: all 0.3s ease;
`;

const NavInner = styled.div`
    ${tw`max-w-[1400px] mx-auto h-full flex items-center justify-between px-6`}
`;

const LeftSection = styled.div`
    ${tw`flex items-center gap-4 md:gap-6`}
`;

const RightSection = styled.div`
    ${tw`flex items-center gap-4`}
`;

const Logo = styled(Link)`
    ${tw`text-xl md:text-2xl font-black tracking-tighter text-white no-underline block`}
    span {
        ${tw`text-indigo-500`}
    }
`;

const SidebarOverlay = styled.div<{ $open: boolean }>`
    ${tw`fixed inset-0 z-[60] transition-opacity duration-300`}
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    opacity: ${props => props.$open ? 1 : 0};
    pointer-events: ${props => props.$open ? 'auto' : 'none'};
`;

const Sidebar = styled.div<{ $open: boolean }>`
    ${tw`fixed top-0 left-0 bottom-0 w-[300px] bg-[#09090b] border-r border-white/10 z-[70] flex flex-col shadow-2xl`}
    transform: translateX(${props => props.$open ? '0%' : '-100%'});
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
`;

const SidebarHeader = styled.div`
    ${tw`flex items-center justify-between p-6 border-b border-white/5`}
`;

const SidebarContent = styled.div`
    ${tw`flex-1 overflow-y-auto p-4 flex flex-col gap-1`}
`;

const NavItem = styled(NavLink)`
    ${tw`flex items-center gap-4 px-4 py-3 rounded-xl text-gray-400 font-medium transition-all duration-200`}
    
    &:hover {
        ${tw`text-white`}
        background-color: rgba(255, 255, 255, 0.05);
    }

    &.active {
        ${tw`text-indigo-400 border border-indigo-500/20`}
        background-color: rgba(79, 70, 229, 0.1);
        box-shadow: 0 0 20px rgba(79, 70, 229, 0.1);
    }
`;

const ServerSectionTitle = styled.button<{ $open: boolean }>`
    ${tw`flex items-center justify-between w-full mt-6 mb-2 px-4 py-3 rounded-xl transition-all duration-300 outline-none border`}
    background-color: ${props => props.$open ? 'rgba(79, 70, 229, 0.1)' : 'rgba(255, 255, 255, 0.03)'};
    border-color: ${props => props.$open ? 'rgba(79, 70, 229, 0.3)' : 'transparent'};
    
    &:hover {
        background-color: ${props => props.$open ? 'rgba(79, 70, 229, 0.15)' : 'rgba(255, 255, 255, 0.05)'};
        border-color: ${props => props.$open ? 'rgba(79, 70, 229, 0.5)' : 'rgba(255, 255, 255, 0.1)'};
    }

    > div {
        ${tw`flex items-center gap-3`}
    }

    .title-text {
        ${tw`text-xs font-bold uppercase tracking-wider transition-colors`}
        color: ${props => props.$open ? '#818cf8' : '#9ca3af'};
    }

    .icon {
        ${tw`w-4 h-4 transition-transform duration-300`}
        color: ${props => props.$open ? '#818cf8' : '#6b7280'};
        transform: ${props => props.$open ? 'rotate(180deg)' : 'rotate(0deg)'};
    }
`;

const SubMenuContainer = styled.div<{ $open: boolean }>`
    display: grid;
    grid-template-rows: ${props => props.$open ? '1fr' : '0fr'};
    transition: grid-template-rows 0.4s ease-in-out, opacity 0.4s ease-in-out, margin 0.4s ease-in-out;
    opacity: ${props => props.$open ? 1 : 0};
    margin-top: ${props => props.$open ? '0.5rem' : '0'};

    > div {
        overflow: hidden;
        ${tw`flex flex-col gap-1`}
        padding-left: 0.5rem;
        border-left: 2px solid rgba(255, 255, 255, 0.05);
        margin-left: 1rem;
    }
`;

const SimpleSectionTitle = styled.div`
    ${tw`text-[10px] font-black text-gray-500 uppercase tracking-widest mt-6 mb-3 px-4`}
`;

const UserFooter = styled.div`
    ${tw`p-5 border-t border-white/5 bg-black/20`}
`;

const MenuButton = styled.button`
    ${tw`w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl text-white transition-all duration-300`}
    background-color: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.05);

    &:hover {
        background-color: rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.5);
        transform: scale(1.05);
    }
`;

export default () => {
    const name = useStoreState((state: ApplicationStore) => state.settings.data!.name);
    const rootAdmin = useStoreState((state: ApplicationStore) => state.user.data!.rootAdmin);
    const user = useStoreState((state: ApplicationStore) => state.user.data!);
    const [isOpen, setIsOpen] = useState(false);
    const [isLoggingOut, setIsLoggingOut] = useState(false);
    const [isServerMenuOpen, setIsServerMenuOpen] = useState(true);
    
    const match = useRouteMatch<{ id: string }>('/server/:id');
    const serverId = match?.params.id;
    const location = useLocation();

    useEffect(() => {
        setIsOpen(false);
    }, [location.pathname]);

    const onTriggerLogout = () => {
        setIsLoggingOut(true);
        http.post('/auth/logout').finally(() => {
            window.location.href = '/';
        });
    };

    return (
        <>
            <SpinnerOverlay visible={isLoggingOut} />
            
            <NavContainer>
                <NavInner>
                    <LeftSection>
                        <MenuButton onClick={() => setIsOpen(true)}>
                            <FontAwesomeIcon icon={faBars} size="lg" />
                        </MenuButton>
                        <Logo to={'/'}>
                            {name.substring(0, 3)}<span>{name.substring(3)}</span>
                        </Logo>
                    </LeftSection>

                    <RightSection>
                        <SearchContainer />
                        <div css={tw`w-10 h-10 rounded-full overflow-hidden border-2 border-white/10 shadow-lg`}>
                            <Avatar.User />
                        </div>
                    </RightSection>
                </NavInner>
            </NavContainer>

            <div css={tw`h-24 w-full`} />

            <SidebarOverlay $open={isOpen} onClick={() => setIsOpen(false)} />
            <Sidebar $open={isOpen}>
                <SidebarHeader>
                    <div css={tw`text-xl font-black text-white tracking-tight`}>
                        Main<span css={tw`text-indigo-500`}>Menu</span>
                    </div>
                    <button onClick={() => setIsOpen(false)} css={tw`text-gray-500 hover:text-white transition-colors`}>
                        <FontAwesomeIcon icon={faTimes} size="lg" />
                    </button>
                </SidebarHeader>

                <SidebarContent>
                    <NavItem to={'/'} exact>
                        <FontAwesomeIcon icon={faServer} css={tw`w-5`} /> Dashboard
                    </NavItem>

                    <SimpleSectionTitle>Account Settings</SimpleSectionTitle>
                    <NavItem to={'/account'} exact>
                        <FontAwesomeIcon icon={faUserCircle} css={tw`w-5`} /> My Account
                    </NavItem>
                    <NavItem to={'/account/api'}>
                        <FontAwesomeIcon icon={faKey} css={tw`w-5`} /> API Credentials
                    </NavItem>
                    <NavItem to={'/account/ssh'}>
                        <FontAwesomeIcon icon={faUnlockAlt} css={tw`w-5`} /> SSH Keys
                    </NavItem>
                    <NavItem to={'/account/activity'}>
                        <FontAwesomeIcon icon={faHistory} css={tw`w-5`} /> Account Activity
                    </NavItem>
                    
                    {serverId && (
                        <>
                            <div css={tw`my-2`} />
                            <ServerSectionTitle $open={isServerMenuOpen} onClick={() => setIsServerMenuOpen(!isServerMenuOpen)}>
                                <div>
                                    <FontAwesomeIcon icon={faServer} css={tw`w-4 h-4`} className="icon" />
                                    <span className="title-text">Management Console</span>
                                </div>
                                <FontAwesomeIcon icon={faChevronDown} className="icon" />
                            </ServerSectionTitle>
                            
                            <SubMenuContainer $open={isServerMenuOpen}>
                                <div>
                                    <NavItem to={`/server/${serverId}`} exact>
                                        <FontAwesomeIcon icon={faTerminal} css={tw`w-5`} /> Terminal
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/files`}>
                                        <FontAwesomeIcon icon={faFolderOpen} css={tw`w-5`} /> File Manager
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/databases`}>
                                        <FontAwesomeIcon icon={faDatabase} css={tw`w-5`} /> Databases
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/schedules`}>
                                        <FontAwesomeIcon icon={faCalendarAlt} css={tw`w-5`} /> Schedules
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/users`}>
                                        <FontAwesomeIcon icon={faUsers} css={tw`w-5`} /> Users
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/backups`}>
                                        <FontAwesomeIcon icon={faCloudDownloadAlt} css={tw`w-5`} /> Backups
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/network`}>
                                        <FontAwesomeIcon icon={faNetworkWired} css={tw`w-5`} /> Network
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/startup`}>
                                        <FontAwesomeIcon icon={faBoxOpen} css={tw`w-5`} /> Startup
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/activity`}>
                                        <FontAwesomeIcon icon={faHistory} css={tw`w-5`} /> Server Activity
                                    </NavItem>
                                    <NavItem to={`/server/${serverId}/settings`}>
                                        <FontAwesomeIcon icon={faCogs} css={tw`w-5`} /> Settings
                                    </NavItem>
                                </div>
                            </SubMenuContainer>
                        </>
                    )}

                    {rootAdmin && (
                        <>
                            <div css={tw`my-2 border-t border-white/5`} />
                            <a href={'/admin'} css={tw`flex items-center gap-4 px-4 py-3 rounded-xl text-red-400 font-bold hover:bg-red-500/10 transition-all mt-2`}>
                                <FontAwesomeIcon icon={faCogs} css={tw`w-5`} /> Admin Area
                            </a>
                        </>
                    )}
                </SidebarContent>

                <UserFooter>
                    <div css={tw`flex items-center gap-4`}>
                        <div css={tw`w-10 h-10 rounded-xl overflow-hidden shadow-lg`}>
                            <Avatar.User />
                        </div>
                        <div css={tw`flex-1 min-w-0`}>
                            <div css={tw`text-sm font-bold text-white truncate`}>{user.username}</div>
                            <div css={tw`text-[10px] text-indigo-300 truncate uppercase tracking-wider`}>Verified User</div>
                        </div>
                        <button onClick={onTriggerLogout} css={tw`text-white/30 hover:text-red-400 transition-colors`}>
                            <FontAwesomeIcon icon={faSignOutAlt} />
                        </button>
                    </div>
                </UserFooter>
            </Sidebar>
        </>
    );
};

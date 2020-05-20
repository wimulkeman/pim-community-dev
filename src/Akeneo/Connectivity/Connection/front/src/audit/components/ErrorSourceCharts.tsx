import React from 'react';
import {Section} from '../../common';
import styled from '../../common/styled-with-theme';
import {AuditEventType} from '../../model/audit-event-type.enum';
import {FlowType} from '../../model/flow-type.enum';
import {Translate} from '../../shared/translate';
import {useWeeklyErrorAudit} from '../api-hooks/use-weekly-error-audit';
import {ConnectionSelect} from '../components/ConnectionSelect';
import {EventChart} from '../components/EventChart';
import {redTheme} from '../event-chart-themes';
import {NoConnection} from './NoConnection';
import useConnectionSelect from '../useConnectionSelect';

const ErrorSourceChartsContainer = styled.div`
    padding-bottom: 25px;
    display: block;
`;
const NoConnectionContainer = styled.div`
    border: 1px solid ${({theme}) => theme.color.grey60};
    padding-bottom: 20px;
    margin-top: 20px;
`;

export const ErrorSourceCharts = () => {
    const {loading, weeklyErrorAudit} = useWeeklyErrorAudit();

    const [connections, selectedConnectionCode, setSelectedConnectionCode] = useConnectionSelect(
        FlowType.DATA_SOURCE
    );

    if (loading || undefined === weeklyErrorAudit) {
        return <>Loading...</>; // TODO Loading spinner
    }

    return (
        <ErrorSourceChartsContainer>
            <Section title='akeneo_connectivity.connection.dashboard.charts.errors_on_source_connections'>
                 {0 !== connections.length && (
                    <ConnectionSelect
                        connections={connections}
                        onChange={code => setSelectedConnectionCode(code)}
                        label={
                            <Translate id='akeneo_connectivity.connection.dashboard.connection_selector.title.source' />
                        }
                    />
                )}
            </Section>

            {0 === connections.length ? (
                <NoConnectionContainer>
                    <NoConnection flowType={FlowType.DATA_SOURCE} />
                </NoConnectionContainer>
            ) : (
                <EventChart
                    eventType={AuditEventType.PRODUCT_READ}
                    theme={redTheme}
                    title={<Translate id='akeneo_connectivity.connection.dashboard.charts.error_count_per_day' />}
                    selectedConnectionCode={selectedConnectionCode}
                    dateFormat={{weekday: 'long', month: 'short', day: 'numeric'}}
                    chartOptions={{height: 283, width: 1000}}
                />
            )}
        </ErrorSourceChartsContainer>
    );
};

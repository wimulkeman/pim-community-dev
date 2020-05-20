import {useQuery} from '../../shared/fetch';

type Result = {
    previous_week: {[datetime: string]: number};
    current_week: {[datetime: string]: number};
    current_week_total: number;
};

const useWeeklyErrorAudit = () => {
    const {loading, data: weeklyErrorAudit} = useQuery<Result>(
        'akeneo_connectivity_connection_audit_rest_weekly_error',
        {}
    );

    return {loading, weeklyErrorAudit};
};

export {useWeeklyErrorAudit};

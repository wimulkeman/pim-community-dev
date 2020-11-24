import React, {FC} from 'react';
import {VictoryAxis, VictoryChart, VictoryLine} from "victory";
import {useTheme} from 'akeneo-design-system';
import {useTranslate} from '@akeneo-pim-community/legacy-bridge';

const UserContext = require('pim/user-context');

const INITIAL_CHART_WIDTH = 600;
const INITIAL_CHART_HEIGHT = 280;

type Props = {
  rawDataset: {[date: string]: string | null};
};

const QualityScoreEvolutionChart: FC<Props> = ({rawDataset}) => {
  const theme = useTheme();
  const translate = useTranslate();

  const formatMonthlyDate = (date: string, index: any) => {
    //The current month is displayed differently
    if (index === 5) {
      return translate('akeneo_data_quality_insights.dqi_dashboard.quality_score_evolution.current_month_label');
    }

    if (date === 'before' || date === 'after') {
      return '';
    }
    const uiLocale = UserContext.get('uiLocale');

    return new Intl.DateTimeFormat(uiLocale.replace('_', '-'), {month: 'short', year: '2-digit'}).format(new Date(date)).replace(/\s/g, '. ');
  };

  const tickValues = [
    'before',
    ...Object.keys(rawDataset),
    'after',
  ];

  const data = [
    { x: 'before', y: typeof rawDataset[Object.keys(rawDataset)[0]] === 'string' ? rawDataset[Object.keys(rawDataset)[0]] : null },
    ...Object.entries(rawDataset).map(([date, _]) => {
      return {x: date, y: rawDataset[date]};
    }),
    { x: 'after', y: rawDataset[Object.keys(rawDataset)[5]] },
  ];

  return (
    <VictoryChart
      height={INITIAL_CHART_HEIGHT}
      width={INITIAL_CHART_WIDTH}
      padding={{top: 30, bottom: 30, left: 20, right: 1}}
      domainPadding={{ x: [-60, -60], y: 2 }}
    >
      <VictoryAxis
        tickValues={tickValues}
        tickFormat={formatMonthlyDate}
        style={{
          axis: {strokeWidth: 0},
          tickLabels: {fontSize: theme.fontSize.small, fill: theme.color.grey120}
        }}
      />

      <VictoryAxis
        dependentAxis
        orientation='left'
        standalone={false}
        tickValues={['E', 'D', 'C', 'B', 'A']}
        style={{
          grid: {
            strokeWidth: 1,
            stroke: theme.color.grey60,
          },
          axis: {strokeWidth: 0},
          tickLabels: {fontSize: theme.fontSize.default, fill: theme.color.grey120, padding: 10}
        }}
      />

      <VictoryLine
        interpolation="step"
        style={{
          data: {
            stroke: theme.color.grey80,
            strokeWidth: 3,
            strokeLinejoin: 'round'
          },
        }}
        data={data}
      />
    </VictoryChart>
  );
};

export {QualityScoreEvolutionChart};

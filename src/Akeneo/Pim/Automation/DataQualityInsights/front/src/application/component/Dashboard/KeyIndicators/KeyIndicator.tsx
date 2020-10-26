import React, {FC} from 'react';
import {useTranslate} from "@akeneo-pim-community/legacy-bridge";

type Data = {
  ratio: number;
  productsNumber: number;
}

type Props = {
  type: string;
  data?: Data;
};

const KeyIndicator: FC<Props> = ({children, type, data}) => {
  const translate = useTranslate();

  return (
    <div>
      {children}
      {translate(`akeneo_data_quality_insights.dqi_dashboard.key_indicators.list.${type}.title`)}
    </div>
  );
}

export {KeyIndicator};

import React, {Children, FC, ReactElement} from 'react';
import {useTranslate} from "@akeneo-pim-community/legacy-bridge";
import {KeyIndicator} from "./KeyIndicator";

const messages = {
  "has_image": {
    50: [
      'message','message','message',
    ],
    75: [
      'message','message','message',
    ],
  }
};

const KeyIndicators: FC = ({children}) => {
  const translate = useTranslate();

  return (
    <div>
      <div className="AknSubsection-title AknSubsection-title--glued">
        <span>{translate('akeneo_data_quality_insights.dqi_dashboard.key_indicators.title')}</span>
      </div>

      {
        Children.map(children, ((child) => {
          const element = child as ReactElement;
          if (element.type === KeyIndicator) {
            return React.cloneElement(element, {type: element.props.type});
          }

          return child;
        }))
      }
    </div>
  );
}

export {KeyIndicators};

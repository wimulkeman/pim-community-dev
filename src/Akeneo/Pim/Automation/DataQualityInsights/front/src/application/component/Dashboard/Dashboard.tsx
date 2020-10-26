import React, {FunctionComponent} from "react";
import Overview from "./Overview/Overview";
import Widgets from "./Widgets/Widgets";
import {AxesContextProvider} from "../../context/AxesContext";
import {KeyIndicators} from "./KeyIndicators/KeyIndicators";
import {KeyIndicator} from "./KeyIndicators/KeyIndicator";
import {AssetsIcon, EditIcon, pimTheme} from "akeneo-design-system";
import {ThemeProvider} from "styled-components";
import {DependenciesProvider} from "@akeneo-pim-community/legacy-bridge";

interface DataQualityInsightsDashboardProps {
  timePeriod: string;
  catalogLocale: string;
  catalogChannel: string;
  familyCode: string | null;
  categoryCode: string | null;
  axes: string[];
}

const Dashboard: FunctionComponent<DataQualityInsightsDashboardProps> = ({timePeriod, catalogLocale, catalogChannel, familyCode, categoryCode, axes}) => {
  return (
    <DependenciesProvider>
      <ThemeProvider theme={pimTheme}>
        <AxesContextProvider axes={axes}>
          <div id="data-quality-insights-activity-dashboard">
            <div className="AknSubsection">
              <Overview catalogLocale={catalogLocale} catalogChannel={catalogChannel} timePeriod={timePeriod} familyCode={familyCode} categoryCode={categoryCode}/>
              <KeyIndicators>
                <KeyIndicator type="has_image">
                  <AssetsIcon/>
                </KeyIndicator>
                <KeyIndicator type="good_enrichment">
                  <EditIcon/>
                </KeyIndicator>
              </KeyIndicators>
              <Widgets catalogLocale={catalogLocale} catalogChannel={catalogChannel}/>
            </div>
          </div>
        </AxesContextProvider>
      </ThemeProvider>
    </DependenciesProvider>
  )
};

export default Dashboard;
